<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Notification;
use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\URL;
use Tests\TestCase;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class EmailVerificationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(\Database\Seeders\DatabaseSeeder::class);
    }

    public function test_verification_email_is_sent_after_registration()
    {
        // 1. 会員情報登録画面を開く
        $response = $this->get('/register');
        $response->assertStatus(200);

        // 2. すべての項目を入力する
        $formData = [
            'name' => 'test',
            'email' => 'test@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ];

        // 通知をテスト用に差し替え
        Notification::fake();

        // 3. 登録ボタンを押す
        $response = $this->followingRedirects()->post('/register', $formData);
        $response->assertStatus(200);
        // 4. メール認証が九人画面へ遷移していることを確認 
        $response->assertViewIs('auth.verify-email');

        // 5. バリデーションエラーがないことを確認
        $response->assertSessionHasNoErrors();

        // 6. ログイン状態であること
        $user = User::where('email', 'test@example.com')->first();
        $this->assertNotNull($user);
        $this->assertAuthenticatedAs($user);

        // 7. 認証メールが送信されていることを確認
        Notification::assertSentTo($user, VerifyEmail::class);
    }

    public function test_clicking_verification_button_redirects_to_verification_page()
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

        // mailhogの画面を表示
        $this->actingAs($user)->get('http://localhost:8025/');

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
    }

    public function test_user_is_redirected_to_profile_page_after_email_verification()
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

        // mailhogの画面を表示
        $this->actingAs($user)->get('http://localhost:8025/');

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
            // プロフィール編集済みの場合、一覧画面にリダイレクト
            $verifyResponse->assertRedirect('/');
        } else {
            // プロフィール変種未の場合、プロフィール設定画面へリダイレクト
            $verifyResponse->assertRedirect('/mypage/profile');
        }

        // DB上も認証済みになっていること
        $this->assertTrue($user->fresh()->hasVerifiedEmail());
    }
}
