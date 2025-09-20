<?php

namespace App\Providers;

use App\Actions\Fortify\CreateNewUser;
use App\Actions\Fortify\ResetUserPassword;
use App\Actions\Fortify\UpdateUserPassword;
use App\Actions\Fortify\UpdateUserProfileInformation;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Str;
use Laravel\Fortify\Fortify;
use App\Http\Requests\LoginRequest as MyLoginRequest;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Laravel\Fortify\Contracts\RegisterResponse;
use Illuminate\Validation\ValidationException;

class FortifyServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // loginページの入力時に適用されるフォームリクエストをFortify既定のものから、自作のものへ差し替え
        $this->app->bind(\Laravel\Fortify\Http\Requests\LoginRequest::class, \App\Http\Requests\LoginRequest::class);

        // ユーザーの新規登録後にプロフィール登録画面へリダイレクトするように設定
        $this->app->singleton(RegisterResponse::class, function () {
            // 無名クラスを作成
            return new class implements RegisterResponse {
                // RegisterResponseに必要なメソッドを実装
                public function toResponse($request)
                {
                    // 登録後のリダイレクト先を指定
                    return redirect('/mypage/profile');
                }
            };
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Fortify::createUsersUsing(CreateNewUser::class);
        /*Fortify::updateUserProfileInformationUsing(UpdateUserProfileInformation::class);
        Fortify::updateUserPasswordsUsing(UpdateUserPassword::class);
        Fortify::resetUserPasswordsUsing(ResetUserPassword::class);

        RateLimiter::for('login', function (Request $request) {
            $throttleKey = Str::transliterate(Str::lower($request->input(Fortify::username())).'|'.$request->ip());

            return Limit::perMinute(5)->by($throttleKey);
        });

        RateLimiter::for('two-factor', function (Request $request) {
            return Limit::perMinute(5)->by($request->session()->get('login.id'));
        });*/
        Fortify::registerView(function () {
            return view('auth.register');
        });
        Fortify::loginView(function () {
            return view('auth.login');
        });
        RateLimiter::for('login', function (Request $request) {
            $email = (string)$request->email;
            return Limit::perMinute(10)->by($email . $request->ip());
        });
        // 自作のフォームリクエストを使用したログイン認証処理
        Fortify::authenticateUsing(function (Request $request) {
            // 自作のフォームリクエストのインスタンスを取得
            $loginReq = app(MyLoginRequest::class);
            // 自作のフォームリクエストに基づいてバリデーションを実施
            $request->validate($loginReq->rules(), $loginReq->messages());
            // usersテーブルから入力したemailと一致するレコードを取得
            $user = User::where('email', $request->input('email'))->first();
            // 取得したmodelインスタンスがnullではなく、usersテーブルのpasswordと入力したパスワードが一致すれば、取得したmodelインスタンスを返す
            if ($user && Hash::check($request->input('password'), $user->password)) {
                return $user;
                // それ以外の場合、パスワード不一致の例外を投げる
            } else {
                throw ValidationException::withMessages([
                    'password' => ['ログイン情報が登録されていません'],
                ]);
            }
            // 該当するモデルインスタンスがない場合、nullを返す。
            return null;
        });
    }
}
