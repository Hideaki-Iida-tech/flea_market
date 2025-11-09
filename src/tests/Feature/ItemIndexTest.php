<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use App\Models\Item;
use App\Models\Condition;
use App\Models\User;
use App\Models\Order;
use Tests\TestCase;

class ItemIndexTest extends TestCase
{
    /**
     * すべての商品が表示されるかどうかのテスト
     *
     *
     */
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(\Database\Seeders\DatabaseSeeder::class);
    }

    /** */
    /* 未ログイン時、シード済み商品が一覧に表示されること
    */
    public function test_index_shows_all_seed_items_for_guest()
    {
        // シードされた商品を取得
        $items = Item::orderBy('id')->get();
        $this->assertGreaterThan(0, $items->count());

        // トップ（一覧）へアクセス（未ログイン）
        $response = $this->get('/');
        $response->assertStatus(200);

        // 各アイテムが一覧に出ているか（名前、詳細リンク）
        foreach ($items as $item) {
            // 商品名
            $response->assertSee($item->item_name, false);

            // 詳細ページへのリンク
            $response->assertSee('item/' . $item->id, false);

            //画像URL
            $response->assertSee($item->image_url, false);
        }
    }

    public function test_purchased_items_display_sold_label()
    {
        // 購入済みアイテム（orderあり）
        $purchasedItem = Item::where('item_name', '腕時計')->first();
        $userId = User::all()->random()->id;
        $buyerId = User::all()->random()->id;

        if (!$purchasedItem) {
            $conditionId = Condition::where('name', '良好')->value('id') ?? Condition::value('id');
            $purchasedItem = Item::create([
                'item_image' => 'https://coachtech-matter.s3.ap-northeast-1.amazonaws.com/image/Armani+Mens+Clock.jpg',
                'condition_id' => $conditionId,
                'item_name' => '腕時計',
                'brand' => 'Rolax',
                'description' => 'スタイリッシュなデザインのメンズ腕時計',
                'price' => 15000,
                'user_id' => $userId,
            ]);
        }

        $profileData = [
            'profile_image' => 'https://www.example.com/sample.jpeg',
            'postal_code' => '000-0000',
            'address' => '東京都葛飾区柴又',
            'building' => 'メゾン柴又',
            'is_profile_completed' => true,
        ];

        User::findOrFail($buyerId)->update($profileData);

        Order::create([
            'item_id' => $purchasedItem->id,
            'user_id' => $buyerId,
            'price' => $purchasedItem->price,
            'address' => User::findOrFail($buyerId)->address,
            'payment_method' => Order::PAYMENT_CARD,
            'postal_code' => User::findOrFail($buyerId)->postal_code,
            'building' => User::findOrFail($buyerId)->building ?? null,
        ]);

        // 未購入アイテム（orderなし）
        $unpurchasedItem = Item::where('item_name', 'HDD')->first();
        $userId = User::all()->random()->id;
        $buyerId = User::all()->random()->id;

        if (!$unpurchasedItem) {
            $conditionId = Condition::where('name', '目立った傷や汚れなし')->value('id') ?? Condition::value('id');
            $unpurchasedItem = Item::create([
                'item_image' => 'https://coachtech-matter.s3.ap-northeast-1.amazonaws.com/image/HDD+Hard+Disk.jpg',
                'condition_id' => $conditionId,
                'item_name' => 'HDD',
                'brand' => '西芝',
                'description' => '高速で信頼性の高いハードディスク',
                'price' => 5000,
                'user_id' => $userId,
            ]);
        }

        // 商品一覧ページを表示
        $response = $this->get('/');
        $html = $response->getContent();
        $response->assertStatus(200);

        // 「購入済みアイテム」には"Sold"が表示されている
        $response->assertSee($purchasedItem->item_name, false);
        $escapedName = preg_quote($purchasedItem->item_name, '/');
        $this->assertMatchesRegularExpression('/' . $escapedName . '\s*<span class="sold">Sold<\/span>/', $html);

        // 「未購入商品」には"Sold"が表示されていない
        $response->assertSee($unpurchasedItem->item_name, false);
        $escapedName = preg_quote($unpurchasedItem->item_name, '/');
        $this->assertDoesNotMatchRegularExpression('/' . $escapedName . '\s*<span class="sold">Sold<\/span>/', $html);
    }

    public function test_own_items_are_not_displayed_in_listing()
    {
        // テストユーザー1のユーザーidを取得（ログイン者）
        $loginUserId = User::where('email', 'test1@example.com')->value('id') ?? User::all()->random()->id;

        // テストユーザー1が出品した商品のモデルインスタンスを取得
        $loginUserItems = Item::where('user_id', $loginUserId)->get();

        // テストユーザー2のユーザーidを取得（非ログイン者）
        $guestUserId = User::where('email', 'test2@example.com')->value('id') ?? User::all()->random()->id;

        // テストユーザー2が出品した商品のモデルインスタンスを取得
        $guestUserItems = Item::where('user_id', $guestUserId)->get();

        // テストユーザー1でログイン状態で一覧画面を表示
        $response = $this->actingAs(User::findOrFail($loginUserId))->get('/');
        $response->assertStatus(200);

        // テストユーザー1（ログイン者）が出品した商品の商品名が表示されていないことを確認
        foreach ($loginUserItems as $item) {
            $response->assertDontSee($item->item_name);
        }

        // テストユーザー2（非ログイン者）が出品した商品の商品名が表示されていることを確認
        foreach ($guestUserItems as $item) {
            $response->assertSee($item->item_name);
        }
    }
}
