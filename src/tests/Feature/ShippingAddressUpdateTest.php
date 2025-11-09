<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\Item;
use App\Models\User;
use App\Models\Order;

class ShippingAddressUpdateTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(\Database\Seeders\DatabaseSeeder::class);
    }

    public function test_updated_shipping_address_is_reflected_on_purchase_page()
    {
        // 購入する商品itemのインスタンスを生成（出品者はテストユーザー1）
        $purchasedItem = Item::where('item_name', '腕時計')->first()
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
        $response = $this->actingAs($buyer)->get('/purchase/'
            . $purchasedItem->id);
        $response->assertStatus(200);

        // 「変更する」ボタンをクリック
        $response = $this->actingAs($buyer)->get('/purchase/address/'
            . $purchasedItem->id);
        $response->assertStatus(200);

        // 送付先住所変更画面で変更後の住所を入力
        $addressData = [
            'postal_code' => '000-0001',
            'address' => '東京都台東区浅草1-2-3',
            'building' => 'メゾン浅草',
        ];

        // 「更新する」ボタンを押して、入力内容を送信
        $response = $this->actingAs($buyer)->post('/purchase/address/'
            . $purchasedItem->id, $addressData);
        $response->assertStatus(302);
        $response->assertRedirect('/purchase/' . $purchasedItem->id);

        // セッションにデータが入っていることを確認
        $this->assertEquals('000-0001', session(
            "order_draft.{$purchasedItem->id}.postal_code"
        ));
        $this->assertEquals('東京都台東区浅草1-2-3', session(
            "order_draft.{$purchasedItem->id}.address"
        ));
        $this->assertEquals('メゾン浅草', session(
            "order_draft.{$purchasedItem->id}.building"
        ));

        // 商品購入画面に、送付先住所変更画面での入力内容が反映されていることを確認
        $response = $this->actingAs($buyer)->get(
            '/purchase/' . $purchasedItem->id
        );
        $response->assertStatus(200);

        $response->assertSee('000-0001', false);
        $response->assertSee('東京都台東区浅草1-2-3', false);
        $response->assertSee('メゾン浅草', false);
    }

    public function test_shipping_address_is_linked_to_purchased_item()
    {
        // 購入する商品itemのインスタンスを生成（出品者はテストユーザー1）
        $purchasedItem = Item::where('item_name', '腕時計')->first()
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
        $response = $this->actingAs($buyer)->get('/purchase/'
            . $purchasedItem->id);
        $response->assertStatus(200);

        // 「変更する」ボタンをクリック
        $response = $this->actingAs($buyer)->get('/purchase/address/'
            . $purchasedItem->id);
        $response->assertStatus(200);

        // 送付先住所変更画面で変更後の住所を入力
        $addressData = [
            'postal_code' => '000-0001',
            'address' => '東京都台東区浅草1-2-3',
            'building' => 'メゾン浅草',
        ];

        // 「更新する」ボタンを押して、入力内容を送信
        $response = $this->actingAs($buyer)->post('/purchase/address/'
            . $purchasedItem->id, $addressData);
        $response->assertStatus(302);
        $response->assertRedirect('/purchase/' . $purchasedItem->id);

        // 購入する商品情報や支払い方法を設定
        $orderData = [
            'user_id' => $buyer->id,
            'item_id' => $purchasedItem->id,
            'price' => $purchasedItem->price,
            'address' => session("order_draft.{$purchasedItem->id}.address"),
            'payment_method' => Order::PAYMENT_CARD,
            'postal_code' => session("order_draft.{$purchasedItem->id}.postal_code"),
            'building' => session("order_draft.{$purchasedItem->id}.building"),
        ];

        // 購入情報をDBに登録
        Order::create($orderData);

        //　postal_code,address,buildingが購入情報に紐づいてDBに登録されていることを確認
        $this->assertDatabaseHas('orders', [
            'user_id' => $buyer->id,
            'item_id' => $purchasedItem->id,
            'price' => $purchasedItem->price,
            'address' => session("order_draft.{$purchasedItem->id}.address"),
            'payment_method' => Order::PAYMENT_CARD,
            'postal_code' => session("order_draft.{$purchasedItem->id}.postal_code"),
            'building' => session("order_draft.{$purchasedItem->id}.building"),
        ]);
    }
}
