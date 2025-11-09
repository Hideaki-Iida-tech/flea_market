<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\Item;
use App\Models\User;
use Laravel\Dusk;

class PaymentMethodUpdateTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(\Database\Seeders\DatabaseSeeder::class);
    }

    public function test_changed_payment_method_is_reflected_on_confirmation_page()
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

        $this->browse(function (Browser $browser) use ($buyer, $purchaedItem) {
            $browser->loginAs($buyer)
                ->visit("/purchase/{$purchasedItem->id}")
                ->assertSee('支払い方法')
                ->select('payment_method', 'card')
                ->pause(500)
                ->assertSeeIn('#selectedValue', 'クレジットカード');
        });
        // 購入ページを表示（テストユーザー3でログイン）
        $response = $this->actingAs($buyer)->get('/purchase/' . $purchasedItem->id);
        $response->assertStatus(200);
    }
}
