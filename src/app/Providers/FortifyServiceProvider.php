<?php

namespace App\Providers;

use App\Actions\Fortify\CreateNewUser;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\ServiceProvider;
use Laravel\Fortify\Fortify;
use App\Http\Requests\LoginRequest as MyLoginRequest;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Laravel\Fortify\Contracts\RegisterResponse;
use Illuminate\Validation\ValidationException;
use Laravel\Fortify\Contracts\LoginResponse;
use App\Actions\Fortify\LoginResponse as CustomLoginResponse;

/**
 * Fortify に関する設定・カスタマイズを行うサービスプロバイダ.
 *
 * - ログインリクエスト(FormRequest)の差し替え
 * - 新規登録後のリダイレクト先の変更
 * - ログイン後レスポンス(LoginResponse)の差し替え
 * - 認証ビュー(view)の指定
 * - ログイン試行回数のレート制限
 * - 独自バリデーションを使ったログイン処理
 */
class FortifyServiceProvider extends ServiceProvider
{
    /**
     * サービスコンテナへのバインドなど、アプリケーションサービスの登録処理.
     *
     * Fortify が内部で解決するクラスを、
     * アプリケーション側で用意したクラスに差し替える設定を行う。
     */
    public function register(): void
    {
        // Fortify が使用する LoginRequest を、自作の LoginRequest に差し替える
        // これにより、ログイン時のバリデーションルールを自由に定義できる
        $this->app->bind(
            \Laravel\Fortify\Http\Requests\LoginRequest::class,
            \App\Http\Requests\LoginRequest::class
        );

        // ユーザーの新規登録後にプロフィール登録画面へリダイレクトするように設定
        // ユーザー新規登録後のレスポンスをカスタマイズ
        // RegisterResponse インターフェイスを実装した無名クラスをシングルトンとして登録
        $this->app->singleton(RegisterResponse::class, function () {
            // 無名クラスを作成
            return new class implements RegisterResponse {
                /**
                 * RegisterResponseに必要なメソッドを実装
                 *
                 * @param  \Illuminate\Http\Request  $request
                 * @return \Illuminate\Http\RedirectResponse
                 */
                public function toResponse($request)
                {
                    // 登録後のリダイレクト先（プロフィール登録画面）を指定
                    return redirect('/mypage/profile');
                }
            };
        });

        // ログイン成功時のレスポンスをカスタムクラスに差し替える
        // App\Actions\Fortify\LoginResponse が実行されるようになる
        $this->app->singleton(LoginResponse::class, CustomLoginResponse::class);
    }

    /**
     * アプリケーション起動時に実行される処理.
     *
     * Fortify の動作（使用するアクション・ビュー・認証ロジック・レート制限など）をここで定義する。
     */
    public function boot(): void
    {
        // ユーザー作成時に使用するアクションを登録
        Fortify::createUsersUsing(CreateNewUser::class);

        // 会員登録画面として使用するビューを指定
        Fortify::registerView(function () {
            return view('auth.register');
        });

        // ログイン画面として使用するビューを指定
        Fortify::loginView(function () {
            return view('auth.login');
        });

        // 1分あたり最大10回まで
        // 「メールアドレス + IPアドレス」の組み合わせをキーとして制限
        RateLimiter::for('login', function (Request $request) {
            $email = (string)$request->email;
            return Limit::perMinute(10)->by($email . $request->ip());
        });

        // 自作フォームリクエストを用いたログイン認証処理の定義
        Fortify::authenticateUsing(function (Request $request) {
            // 自作の LoginRequest(FormRequest) を解決
            $loginReq = app(MyLoginRequest::class);

            // 自作フォームリクエストのルール・メッセージに基づいてバリデーションを実行
            $request->validate($loginReq->rules(), $loginReq->messages());

            // 入力された email に一致するユーザーを取得
            $user = User::where('email', $request->input('email'))->first();

            // ユーザーが存在し、かつパスワードが一致する場合は User モデルを返す
            if ($user && Hash::check($request->input('password'), $user->password)) {
                return $user;
                // それ以外の場合はバリデーションエラーとして扱い、例外を投げる
                // （このメッセージは password フィールドに紐づく）
            } else {
                throw ValidationException::withMessages([
                    'password' => ['ログイン情報が登録されていません'],
                ]);
            }

            // ここに到達した場合は null を返す（認証失敗扱い）
            // ※ 実際には上の例外で処理が終了するため到達しないが、
            //   authenticateUsing のコールバック仕様として null を返す形も想定されている
            return null;
        });
    }
}
