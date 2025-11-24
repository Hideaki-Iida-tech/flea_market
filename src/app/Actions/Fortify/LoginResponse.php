<?php

namespace App\Actions\Fortify;

use Laravel\Fortify\Contracts\LoginResponse as LoginResponseContract;

/**
 * カスタムログインレスポンスクラス
 *
 * Fortify の LoginResponseContract を実装し、
 * ログイン成功後のリダイレクト先をアプリ独自のルールで制御する。
 *
 * 挙動：
 * - メール未認証ユーザー：認証案内ページ（verification.notice）へリダイレクト
 * - 認証済みユーザー：トップページ（/）へリダイレクト
 */
class LoginResponse implements LoginResponseContract
{
    /**
     * ログイン成功時に Fortify から呼び出されるレスポンス生成メソッド
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function toResponse($request)
    {
        // 現在ログインしているユーザーを取得
        $user = $request->user();

        /**
         * メール認証が有効な場合、未認証ユーザーは
         * 「メール認証案内ページ」へリダイレクトさせる。
         *
         * method_exists() を使うのは、メール認証機能が
         * 有効でない環境でもエラーを出さないための安全策。
         */
        if ($user && method_exists($user, 'hasVerifiedEmail') && !$user->hasVerifiedEmail()) {
            return redirect()->route('verification.notice');
        }
        // 認証済みのユーザーは商品一覧ページへ遷移
        return redirect('/');
    }
}
