<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;
use App\Models\Item;
use App\Models\User;

class LikeFeatureTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(\Database\Seeders\DatabaseSeeder::class);
    }

    public function test_user_can_like_item_by_clicking_icon()
    {
        // 詳細を表示する商品itemのインスタンスを生成
        $item = Item::where('item_name', '腕時計')->first()
            ?? Item::inRandomOrder()->first();
        // いいねするログインユーザーのインスタンスを生成
        $user = User::where('name', 'テストユーザー1')->first()
            ?? User::inRandomOrder()->first();

        // 生成したuserについて、メール認証済みにする
        $user->markEmailAsVerified();

        // 商品一覧ページを表示
        $response = $this->get('/item/' . $item->id);
        $response->assertStatus(200);

        // いいねをする
        $response = $this->actingAs($user)->post('/item/' . $item->id . '/like', []);

        // 元のページにリダイレクト
        $response->assertStatus(302);
        $response->assertRedirect('/item/' . $item->id);

        // ログインユーザーで当該商品がいいねされているかどうかを確認
        $this->assertDatabaseHas('item_likes', [
            'user_id' => $user->id,
            'item_id' => $item->id,
        ]);
    }

    public function test_liked_item_icon_changes_color()
    {
        // 詳細を表示する商品itemのインスタンスを生成
        $item = Item::where('item_name', '腕時計')->first()
            ?? Item::inRandomOrder()->first();
        // いいねするログインユーザーのインスタンスを生成
        $user = User::where('name', 'テストユーザー1')->first()
            ?? User::inRandomOrder()->first();

        // 生成したuserについて、メール認証済みにする
        $user->markEmailAsVerified();

        // 商品一覧ページを表示
        $response = $this->get('/item/' . $item->id);
        $response->assertStatus(200);

        // いいね前のアイコン画像を確認
        $response->assertSee(asset('images/like_off.png'));

        // いいねをする
        $response = $this->actingAs($user)->post('/item/' . $item->id . '/like', []);

        // 元のページにリダイレクト
        $response->assertStatus(302);
        $response->assertRedirect('/item/' . $item->id);

        // リダイレクト後のページを再リクエスト
        $response = $this->get('/item/' . $item->id);

        // いいね後のアイコン画像を確認
        $response->assertSee(asset('images/like_on.png'));
    }

    public function test_user_can_unlike_item_by_clicking_icon()
    {
        // 詳細を表示する商品itemのインスタンスを生成
        $item = Item::where('item_name', '腕時計')->first()
            ?? Item::inRandomOrder()->first();
        // いいねするログインユーザーのインスタンスを生成
        $user = User::where('name', 'テストユーザー1')->first()
            ?? User::inRandomOrder()->first();

        // 生成したuserについて、メール認証済みにする
        $user->markEmailAsVerified();

        // 商品一覧ページを表示
        $response = $this->get('/item/' . $item->id);
        $response->assertStatus(200);

        // いいねをする
        $response = $this->actingAs($user)->post('/item/' . $item->id . '/like', []);

        // 元のページにリダイレクト
        $response->assertStatus(302);
        $response->assertRedirect('/item/' . $item->id);

        // ログインユーザーで当該商品がいいねされているかを確認
        $this->assertDatabaseHas('item_likes', [
            'user_id' => $user->id,
            'item_id' => $item->id,
        ]);

        // もう一度いいねをクリックする
        $response = $this->actingAs($user)->post('/item/' . $item->id . '/like', []);

        // ログインユーザーでの当該商品のいいねが解除されているかを確認
        $this->assertDatabaseMissing('item_likes', [
            'user_id' => $user->id,
            'item_id' => $item->id,
        ]);
    }
}
