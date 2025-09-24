<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\ProfileRequest;
use Illuminate\Support\Facades\Storage;

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
        $user = auth()->user();
        // テキスト項目を変数に格納
        $data = $request->only(['name', 'postal_code', 'address', 'building', 'is_profile_completed']);
        // 画像が選択させている場合は保存
        if ($request->hasFile('profile_image')) {
            // 旧ファイルを削除
            if ($user->profile_image) {
                Storage::disk('public')->delete($user->profile_image);
            }
            // publicディスクに保存
            $path = $request->file('profile_image')->store('profiles', 'public');
            // DBには'profiles/xxx.jpeg'を保存
            $data['profile_image'] = $path;
        }
        //$data['is_profile_completed'] = true;
        $user->update($data);
        return redirect('/mypage');
    }
}
