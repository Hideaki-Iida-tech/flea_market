<?php

namespace App\Actions\Fortify;

use Laravel\Fortify\Contracts\LoginResponse as LoginResponseContract;

class LoginResponse implements LoginResponseContract
{
    public function toResponse($request)
    {
        $user = $request->user();

        // 未認証なら認証案内へ
        if ($user && method_exists($user, 'hasVerifiedEmail') && !$user->hasVerifiedEmail()) {
            return redirect()->route('verification.notice');
        }
        // 認証済みは HOMEへ
        return redirect('/');
    }
}
