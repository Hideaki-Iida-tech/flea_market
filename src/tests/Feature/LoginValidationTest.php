<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;

class LoginValidationTest extends TestCase
{

    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(\Database\Seeders\DatabaseSeeder::class);
    }
    /**
     *  メールアドレスが未入力の場合にエラーになることをテスト
     */
    public function test_login_fails_when_email_is_missing()
    {
        // 1. ログイン画面を表示
        $response = $this->get('/login');
        $response->assertStatus(200);

        // 2. メールアドレスを入力せずパスワードのみ入力
        $formData = [
            'email' => '',
            'password' => 'password123'
        ];

        // 3. ログインボタンを押す
        $response = $this->post('/login', $formData);

        // 4. バリデーションエラーが発生することを確認
        $response->assertSessionHasErrors(['email']);

        // 5. リダイレクトすることを確認
        $response->assertStatus(302);
        $response->assertRedirect('/login');

        // 6. エラーメッセージがセッションに入っているか確認
        $this->assertTrue(session()->has('errors'));
        $this->assertEquals(session('errors')->first('email'), 'メールアドレスを入力してください');
    }
    /**
     *  パスワードが未入力の場合にエラーになることをテスト
     */
    public function test_login_fails_when_password_is_missing()
    {
        // 1. ログイン画面を表示
        $response = $this->get('/login');
        $response->assertStatus(200);

        // 2. メールアドレスのみ入力し、パスワードは未入力
        $formData = [
            'email' => 'test@example.com',
            'password' => '',
        ];

        // 3. ログインボタンを押す
        $response = $this->post('/login', $formData);

        // 4. バリデーションエラーが発生することを確認
        $response->assertSessionHasErrors(['password']);

        // 5. リダイレクトすることを確認
        $response->assertStatus(302);
        $response->assertRedirect('/login');

        // 6. エラーメッセージがセッションに入っているか確認
        $this->assertTrue(session()->has('errors'));
        $this->assertEquals(session('errors')->first('password'), 'パスワードを入力してください');
        $response->assertSessionHas('_old_input.email', 'test@example.com');
    }
    /**
     *  間違ったメールアドレス、パスワードを入力した場合にエラーになることをテスト
     */
    public function test_login_fails_with_invalid_credentials()
    {
        // 1. ログイン画面を表示する
        $response = $this->get('/login');
        $response->assertStatus(200);

        // 2. 間違った入力情報を入力
        $formData = [
            'email' => 'test10@example.com',
            'password' => 'password100'
        ];

        // 3. ログインボタンを押す
        $response = $this->post('/login', $formData);

        // 4. バリデーションエラーが発生することを確認
        $response->assertSessionHasErrors(['email']);

        // 5. リダイレクトすることを確認
        $response->assertStatus(302);
        $response->assertRedirect('/login');

        // 6. エラーメッセージがセッションに入っているか確認
        $this->assertTrue(session()->has('errors'));
        $this->assertEquals(session('errors')->first('email'), 'ログイン情報が登録されていません');
        $response->assertSessionHas('_old_input.email', 'test10@example.com');
    }
    /**
     *  すべての項目にバリデーション通りの値を入力した場合のテスト
     */
    public function test_login_succeeds_with_valid_data()
    {
        // 1. ログイン画面を表示
        $response = $this->get('/login');
        $response->assertStatus(200);

        // 2. すべての項目に適正な値を入力
        $formData = [
            'email' => 'test1@example.com',
            'password' => 'password',
        ];

        // 3. ログインボタンを押す
        $response = $this->post('/login', $formData);

        // 4. 正常にリダイレクトされることを確認
        $response->assertStatus(302);

        if (User::where('email', 'test1@example.com')->first()->email_varified_at !== null) {
            $response->assertRedirect('/');
        } else {
            $response->assertRedirect(route('verification.notice'));
        }

        // 5. バリデーションエラーが発生しないことを確認
        $response->assertSessionHasNoErrors();

        // 6. ログイン状態であること
        $user = User::where('email', 'test1@example.com')->first();
        $this->assertAuthenticatedAs($user);
    }
}
