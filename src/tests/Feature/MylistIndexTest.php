<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;
use App\Models\Item;
use App\Models\User;
use App\Models\Order;
use App\Models\Condition;

class MylistIndexTest extends TestCase
{
    /**
     * マイリスト一覧取得テスト
     *
     * 
     */

    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(\Database\Seeders\DatabaseSeeder::class);
    }

    public function test_only_liked_items_are_displayed()
    {
        // ログイン用userインスタンスをランダムに取得
        $user = User::all()->random();
        // いいね用のitemインスタンスをランダムに取得
        $item = Item::all()->random();

        // $itemにいいねをする
        $item->likes()->syncWithoutDetaching([$user->id]);

        // 中間テーブルから、ログインユーザーがいいねをしているレコードのitem_idを取得
        $likedItemsIndex = DB::table('item_likes')
            ->where('user_id', $user->id)
            ->pluck('item_id')
            ->unique()
            ->values();

        $likedItems = Item::whereIn('id', $likedItemsIndex)->get();

        $unlikedItems = Item::when(
            $likedItemsIndex->isNotEmpty(),
            fn($query) => $query->whereNotIn('id', $likedItemsIndex)
        )->get();

        // ログイン状態でマイリスト一覧ページを開く
        $response = $this->actingAs($user)->get('/?tab=mylist');
        $response->assertStatus(200);

        // ログインユーザーがいいねをしているすべてのアイテムについて以下の項目が表示されているかをチェック
        foreach ($likedItems as $item) {
            // 商品名
            $response->assertSee($item->item_name, false);

            // 詳細ページへのリンク
            $response->assertSee('/item/' . $item->id, false);

            //画像URL
            $response->assertSee($item->image_url, false);
        }
        // ログインユーザーがいいねをしていないすべてのアイテムについて以下の項目が表示されていないことをチェック
        foreach ($unlikedItems as $item) {
            // 商品名
            $response->assertDontSee($item->item_name, false);

            // 詳細ページへのリンク
            $response->assertDontSee('/item/' . $item->id, false);

            //画像URL
            $response->assertDontSee($item->image_url, false);
        }
    }

    public function test_sold_label_is_displayed_for_purchased_items_in_liked_list()
    {
        // ログイン用userインスタンスをランダムに取得
        $user = User::all()->random();

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
            'profile_image' => 'http://www.example.com/sample.jpeg',
            'postal_code' => '000-0000',
            'address' => '東京都葛飾区柴又',
            'building' => 'メゾン柴又',
            'is_profile_completed' => true,
        ];

        // 購入者のプロフィール項目を追加更新
        User::findOrFail($buyerId)->update($profileData);

        // 購入処理
        Order::create([
            'item_id' => $purchasedItem->id,
            'user_id' => $buyerId,
            'price' => $purchasedItem->price,
            'address' => User::findOrFail($buyerId)->address,
            'payment_method' => Order::PAYMENT_CARD,
            'postal_code' => User::findOrFail($buyerId)->postal_code,
            'building' => User::findOrFail($buyerId)->building ?? null,
        ]);
        // 購入アイテムにいいねをする
        $purchasedItem->likes()->syncWithoutDetaching([$user->id]);

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

        // 未購入アイテムにいいねをする
        $unpurchasedItem->likes()->syncWithoutDetaching([$user->id]);

        // ログイン状態でマイリスト一覧ページを開く
        $response = $this->actingAs($user)->get('/?tab=mylist');
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

    public function test_mylist_shows_nothing_when_user_is_not_authenticated()
    {
        // アイテムをすべて取得
        $items = Item::all();

        // 未認証状態で、マイリストページを開く
        $response = $this->get('/?tab=mylist');
        $response->assertStatus(200);

        // ビュー変数「items」が空コレクションであること
        $response->assertViewHas('items', function ($items) {
            return $items instanceof \Illuminate\Support\Collection && $items->isEmpty();
        });

        // HTML上に商品が何も表示されていない
        $response->assertDontSee('<div class="items-image">', false);
    }
}
