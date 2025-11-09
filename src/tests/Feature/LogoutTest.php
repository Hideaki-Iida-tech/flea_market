<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Config;
use Illuminate\Auth\Notifications\VerifyEmail;
use Tests\TestCase;
use App\Models\User;

class LogoutTest extends TestCase
{

    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(\Database\Seeders\DatabaseSeeder::class);
    }
    /**
     * 
     *ログアウト機能のテスト
     * 
     */
    public function test_logout()
    {
        // 1. ログインする
        $response = $this->get('/login');
        $response->assertStatus(200);

        $formData = [
            'email' => 'test1@example.com',
            'password' => 'password',
        ];

        $response = $this->post('/login', $formData);

        $user = User::where('email', 'test1@example.com')->first();

        $response->assertStatus(302);
        $response->assertRedirect('/email/verify');

        // 3. 検証メールを再送
        Notification::fake();

        // ログイン状態を明示
        $this->actingAs($user);

        // Fortify等の「検証メール再送ルート」
        $this->post(route('verification.send'));

        // VerifyEmail通知が送られたこと＆URLを取り出す
        $verificationUrl = null;
        Notification::assertSentTo(
            $user,
            VerifyEmail::class,
            function ($notification) use ($user, &$verificationUrl) {
                $mail = $notification->toMail($user);

                // $mail->actionUrlをそのまま使用
                if (property_exists($mail, 'actionUrl') && $mail->actionUrl) {
                    $verificationUrl = $mail->actionUrl;
                    return true;
                }

                // 万一actionUrlが取れない場合に備えて、テスト内でURLを自前生成
                if (!$verificationUrl) {
                    $verificationUrl = URL::temporarySignedRoute(
                        'verification.verify',
                        now()->addMinutes(Config::get('auth.verification.expire', 60)),
                        ['id' => $user->getKey(), 'hash' => sha1($user->email)]
                    );
                }

                return true;
            }
        );

        $this->assertNotNull($verificationUrl, '検証URLを取得できませんでした。');

        // 4. 「メール内の認証ボタンをクリックした」想定で、検証URLにアクセス
        $verifyResponse = $this->get($verificationUrl);
        $verifyResponse->assertStatus(302);

        if ($user->is_prifile_completed) {
            $verifyResponse->assertRedirect('/');
        } else {
            $verifyResponse->assertRedirect('/mypage/profile');
        }

        // DB上も認証済みになっていること
        $this->assertTrue($user->fresh()->hasVerifiedEmail());

        // 5. ログアウトする
        $response = $this->post('/logout');

        // 6. リダイレクトされることを確認する
        $response->assertStatus(302);
        $response->assertRedirect('/');
    }
}
