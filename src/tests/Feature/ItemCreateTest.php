<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use App\Models\Condition;
use App\Models\Item;
use App\Models\Category;
use Illuminate\Support\Facades\Storage;

class ItemCreateTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(\Database\Seeders\DatabaseSeeder::class);
    }

    public function test_item_can_be_created_with_required_information()
    {
        // ログインユーザーのインスタンスを生成（テストユーザー1）
        $user = User::where('name', 'テストユーザー1')->first()
            ?? User::inRandomOrder()->first();

        // 生成したuesrについて、メール認証済みにする
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

        // 出品画面を表示
        $request = $this->actingAs($user)->get('/sell');
        $request->assertStatus(200);

        // 出品する商品の情報を設定

        $categories = [
            Category::where('name', 'ファッション')->first()->id,
            Category::where('name', 'レディース')->first()->id,
            Category::where('name', 'コスメ')->first()->id,
        ];
        $conditionId = Condition::inRandomOrder()->first()->id;
        $itemData = [
            'current_item_image' => 'tmp/items/example.jpeg',
            'condition_id' => $conditionId,
            'categories' => $categories,
            'item_name' => 'テストアイテム1',
            'brand' => 'テストブランド',
            'description' => 'テスト説明',
            'price' => 1000,
        ];

        // テスト用にダミーの画像ファイルを一時フォルダに作成
        Storage::disk('public')->put('tmp/items/example.jpeg', 'dummy image content');

        // 「出品する」ボタンを押す
        $response = $this->actingAs($user)->post('/sell', $itemData);
        $response->assertStatus(302);
        $response->assertRedirect('/sell/success');

        // 各DBに情報が存在することを確認
        $this->assertDatabaseHas('items', [
            'condition_id' => $conditionId,
            'item_name' => 'テストアイテム1',
            'brand' => 'テストブランド',
            'description' => 'テスト説明',
            'price' => 1000,
            'user_id' => $user->id,
        ]);

        $item = Item::where('item_name', 'テストアイテム1')->firstOrFail();

        // DBの他のカラムは assertDatabaseHas で確認しつつ、画像は形式で確認
        $this->assertTrue(str_starts_with($item->item_image, 'items/'));
        $this->assertTrue(\Illuminate\Support\Str::of($item->item_image)
            ->endsWith('.jpeg'));
        $this->assertTrue(Storage::disk('public')->exists($item->item_image)); // 実際に保存されたか

        // 1つ目のカテゴリーが登録されていることを確認
        $this->assertDatabaseHas('category_item', [
            'item_id' => $item->id,
            'category_id' => Category::where('name', 'ファッション')->first()->id,
        ]);

        // 2つ目のカテゴリーが登録されていることを確認
        $this->assertDatabaseHas('category_item', [
            'item_id' => $item->id,
            'category_id' => Category::where('name', 'レディース')->first()->id,
        ]);

        // 3つ目のカテゴリーが登録されていることを確認
        $this->assertDatabaseHas('category_item', [
            'item_id' => $item->id,
            'category_id' => Category::where('name', 'コズメ')->first()->id,
        ]);
    }
}
