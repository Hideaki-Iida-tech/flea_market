<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;

class UserInformationUpdateTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(\Database\Seeders\DatabaseSeeder::class);
    }

    public function test_existing_user_information_is_prepopulated_in_edit_form()
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

        // プロフィールページを表示
        $response = $this->actingAs($user)->get('/mypage');
        $response->assertStatus(200);

        // プロフィール変更画面を表示
        $response = $this->actingAs($user)->get('/mypage/profile');
        $response->assertStatus(200);

        // プロフィール変更画面に初期値が設定されていることを確認
        $response->assertSee($user->image_url, false); // プロフィール画像のパス
        $response->assertSee($user->name, false); // ユーザー名
        $response->assertSee($user->postal_code, false); // 郵便番号
        $response->assertSee($user->address, false); // 住所
        if (isset($user->building)) {
            $response->assertSee($user->building, false); // 建物名
        }
    }
}
