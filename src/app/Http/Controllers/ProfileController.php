<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\ProfileRequest;
use Illuminate\Support\Facades\Auth;

class ProfileController extends Controller
{
    // プロフィール編集画面の表示アクションメソッド
    public function edit()
    {
        return view('profile.edit');
    }

    // プロフィールの更新アクションメソッド
    public function update(ProfileRequest $request)
    {
        $user = Auth::user();
        $request->merge(['is_profile_completed' => true]);
        $user->update($request->only(['postal_code', 'address', 'building', 'profile_image', 'is_profile_completed']));
        return redirect('/');
    }
}
