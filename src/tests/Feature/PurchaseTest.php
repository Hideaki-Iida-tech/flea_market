<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use App\Models\Item;
use App\Models\Order;

class PurchaseTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(\Database\Seeders\DatabaseSeeder::class);
    }

    public function test_user_can_complete_purchase_by_clicking_buy_button()
    {
        // 購入する商品itemのインスタンスを生成
        $item = Item::where('item_name', '腕時計')->first()
            ?? Item::inRandomOrder()->first();
        // 購入をするユーザーのインスタンスを生成
        $buyer = User::where('name', 'テストユーザー2')->first()
            ?? User::inRandomOrder()->first();

        // 生成したbuyerについて、メール認証済みにする
        $buyer->markEmailAsVerified();

        // buyerのプロフィールを設定
        $profileData = [
            'profile_image' => 'https://www.example.com/sample.jpeg',
            'postal_code' => '000-0000',
            'address' => '東京都葛飾区柴又',
            'building' => 'メゾン柴又',
            'is_profile_completed' => true,
        ];
        $buyer->update($profileData);

        // 購入ページを表示
        $response = $this->actingAs($buyer)->get('/purchase/' . $item->id);
        $response->assertStatus(200);

        // 購入する商品情報や支払い方法を設定
        $orderData = [
            'user_id' => $buyer->id,
            'item_id' => $item->id,
            'price' => $item->price,
            'address' => $buyer->address,
            'payment_method' => Order::PAYMENT_CARD,
            'postal_code' => $buyer->postal_code,
            'building' => $buyer->building,
        ];

        // 購入情報をDBに登録
        Order::create($orderData);

        // 購入情報がDBに登録されていることを確認
        $this->assertDatabaseHas('orders', [
            'user_id' => $buyer->id,
            'item_id' => $item->id,
            'price' => $item->price,
            'address' => $buyer->address,
            'payment_method' => Order::PAYMENT_CARD,
            'postal_code' => $buyer->postal_code,
            'building' => $buyer->building,
        ]);
    }

    public function test_purchased_items_display_sold_label_on_item_index()
    {
        // 購入する商品itemのインスタンスを生成（出品者はテストユーザー1）
        $purchasedItem = Item::where('item_name', '腕時計')->first()
            ?? Item::inRandomOrder()->first();

        // 購入していない商品のitemインスタンスを生成（出品者はテストユーザー2）
        $unpurchasedItem = Item::where('item_name', 'HDD')->first()
            ?? Item::inRandomOrder()->first();

        // 購入をするユーザーのインスタンスを生成（テストユーザー3）
        $buyer = User::where('name', 'テストユーザー3')->first()
            ?? User::inRandomOrder()->first();

        // 生成したbuyerについて、メール認証済みにする
        $buyer->markEmailAsVerified();

        // buyerのプロフィールを設定
        $profileData = [
            'profile_image' => 'https://www.example.com/sample.jpeg',
            'postal_code' => '000-0000',
            'address' => '東京都葛飾区柴又',
            'building' => 'メゾン柴又',
            'is_profile_completed' => true,
        ];
        $buyer->update($profileData);

        // 購入ページを表示（テストユーザー3でログイン）
        $response = $this->actingAs($buyer)->get('/purchase/' . $purchasedItem->id);
        $response->assertStatus(200);

        // 購入する商品情報や支払い方法を設定
        $orderData = [
            'user_id' => $buyer->id, // （購入者はテストユーザー3）
            'item_id' => $purchasedItem->id,
            'price' => $purchasedItem->price,
            'address' => $buyer->address,
            'payment_method' => Order::PAYMENT_CARD,
            'postal_code' => $buyer->postal_code,
            'building' => $buyer->building,
        ];

        // 購入情報をDBに登録
        Order::create($orderData);

        // 一覧画面を表示
        $response = $this->get('/');
        $html = $response->getContent();
        $response->assertStatus(200);

        // 「購入済みアイテム」には"Sold"が表示されている（腕時計の出品者はテストユーザー1≠購入者）
        $response->assertSee($purchasedItem->item_name, false);
        $escapedName = preg_quote($purchasedItem->item_name, '/');
        $this->assertMatchesRegularExpression('/' . $escapedName . '\s*<span class="sold">Sold<\/span>/', $html);

        // 「未購入商品」には"Sold"が表示されていない（HDDの出品者はテストユーザー2≠購入者）
        $response->assertSee($unpurchasedItem->item_name, false);
        $escapedName = preg_quote($unpurchasedItem->item_name, '/');
        $this->assertDoesNotMatchRegularExpression('/' . $escapedName . '\s*<span class="sold">Sold<\/span>/', $html);
    }

    public function test_purchased_item_is_displayed_in_pruchase_history()
    {
        // 購入する商品itemのインスタンスを生成（出品者はテストユーザー1）
        $purchasedItem = Item::where('item_name', '腕時計')->first()
            ?? Item::inRandomOrder()->first();

        // 購入していない商品のitemインスタンスを生成（出品者はテストユーザー2）
        $unpurchasedItem = Item::where('item_name', 'HDD')->first()
            ?? Item::inRandomOrder()->first();

        // 購入をするユーザーのインスタンスを生成（テストユーザー3）
        $buyer = User::where('name', 'テストユーザー3')->first()
            ?? User::inRandomOrder()->first();

        // 生成したbuyerについて、メール認証済みにする
        $buyer->markEmailAsVerified();

        // buyerのプロフィールを設定
        $profileData = [
            'profile_image' => 'https://www.example.com/sample.jpeg',
            'postal_code' => '000-0000',
            'address' => '東京都葛飾区柴又',
            'building' => 'メゾン柴又',
            'is_profile_completed' => true,
        ];
        $buyer->update($profileData);

        // 購入ページを表示（テストユーザー3でログイン）
        $response = $this->actingAs($buyer)->get('/purchase/' . $purchasedItem->id);
        $response->assertStatus(200);

        // 購入する商品情報や支払い方法を設定
        $orderData = [
            'user_id' => $buyer->id, // （購入者はテストユーザー3）
            'item_id' => $purchasedItem->id,
            'price' => $purchasedItem->price,
            'address' => $buyer->address,
            'payment_method' => Order::PAYMENT_CARD,
            'postal_code' => $buyer->postal_code,
            'building' => $buyer->building,
        ];

        // 購入情報をDBに登録
        Order::create($orderData);

        // プロフィール画面を表示
        $response = $this->actingAs($buyer)->get('/mypage');
        $response->assertStatus(200);

        // プロフィール画面の「購入した商品」タブを開く
        $response = $this->actingAs($buyer)->get('/mypage?page=buy');
        $response->assertStatus(200);

        // ログインユーザーが購入しているすべてのアイテムについて以下の項目が表示されているかをチェック
        // 商品名
        $response->assertSee($purchasedItem->item_name, false);

        // 詳細ページへのリンク
        $response->assertSee('/item/' . $purchasedItem->id, false);

        //画像URL
        $response->assertSee($purchasedItem->image_url, false);

        // ログインユーザーが購入をしていないすべてのアイテムについて以下の項目が表示されていないことをチェック
        // 商品名
        $response->assertDontSee($unpurchasedItem->item_name, false);

        // 詳細ページへのリンク
        $response->assertDontSee('/item/' . $unpurchasedItem->id, false);

        //画像URL
        $response->assertDontSee($unpurchasedItem->image_url, false);
    }
}
