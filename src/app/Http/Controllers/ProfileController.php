<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\ProfileRequest;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

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

        // hidden から一時パスを取得
        $tmpPath = $request->input('current_profile_image');
        $finalPath = null;


        if ($tmpPath) {
            if (!str_starts_with($tmpPath, 'tmp/profiles/')) {
                abort(422, 'Invalid temporary path');
            }

            $ext = pathinfo($tmpPath, PATHINFO_EXTENSION);
            $finalPath = 'items/' . Str::uuid() . ($ext ? ".{$ext}" : '');

            Storage::disk('public')->move($tmpPath, $finalPath);
            if ($user->profile_image) {
                Storage::disk('public')->delete($user->profile_image);
            }
            $data['profile_image'] = $finalPath;
        }

        /*// 画像が選択させている場合は保存
        if ($request->hasFile('profile_image')) {
            // 旧ファイルを削除
            if ($user->profile_image) {
                Storage::disk('public')->delete($user->profile_image);
            }
            // publicディスクに保存
            $path = $request->file('profile_image')->store('profiles', 'public');
            // DBには'profiles/xxx.jpeg'を保存
            $data['profile_image'] = $path;
        }*/
        //$data['is_profile_completed'] = true;
        $user->update($data);
        return redirect('/mypage');
    }
}
