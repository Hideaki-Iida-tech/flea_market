<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\Item;
use App\Models\User;

class UserInformationRetrievalTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(\Database\Seeders\DatabaseSeeder::class);
    }

    public function test_user_information_including_profile_and_items_is_retrieved_successfully()
    {
        // ログインユーザーのインスタンスを生成（テストユーザー1）
        $user = User::where('name', 'テストユーザー1')->first()
            ?? User::inRandomOrder()->first();

        // 生成したbuyerについて、メール認証済みにする
        $user->markEmailAsVerified();

        // userのプロフィールを設定
        $profileData = [
            'profile_image' => 'https://www.example.com/sample.jpeg',
            'postal_code' => '000-0000',
            'address' => '東京都葛飾区柴又',
            'building' => 'メゾン柴又',
            'is_profile_completed' => true,
        ];
        $user->update($profileData);

        // プロフィールページを表示
        $response = $this->actingAs($user)->get('/mypage');
        $response->assertStatus(200);

        // プロフィール画像、ユーザー名が表示されているかを確認
        $response->assertSee($user->name, false);
        $response->assertSee($user->profile_image, false);

        // 「出品した商品」一覧タブを表示
        $response = $this->actingAs($user)->get('/mypage?page=sell');
        $sellItems = Item::where('user_id', $user->id)->get();
        foreach ($sellItems as $item) {
            // 商品名
            $response->assertSee($item->item_name, false);

            // 詳細ページへのリンク
            $response->assertSee('item/' . $item->id, false);

            //画像URL
            $response->assertSee($item->image_url, false);
        }

        // 「購入した商品」一覧タブを表示
        $response = $this->actingAs($user)->get('/mypage?page=buy');
        $buyItems = Item::whereHas('order', fn($query) => $query
            ->where('user_id', $user->id))
            ->with(['order' => fn($query) => $query
                ->where('user_id', $user->id)])->get();

        foreach ($buyItems as $item) {
            // 商品名
            $response->assertSee($item->item_name, false);

            // 詳細ページへのリンク
            $response->assertSee('item/' . $item->id, false);

            //画像URL
            $response->assertSee($item->image_url, false);
        }
    }
}
