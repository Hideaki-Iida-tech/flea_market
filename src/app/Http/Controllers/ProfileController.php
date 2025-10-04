<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\ProfileRequest;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use App\Models\Item;

class ProfileController extends Controller
{
    public function index(Request $request)
    {
        $isSell = $request->page === 'sell';
        $isBuy = $request->page === 'buy';
        $items = null;

        if ($isSell) {
            $items = Item::where('user_id', auth()->id())->get();
        } elseif ($isBuy) {
            $items = Item::whereHas('order', fn($query) => $query->where('user_id', auth()->id()))->with(['order' => fn($query) => $query->where('user_id', auth()->id())])->get();
        }
        return view('profile.index', compact('items'));
    }

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
            if (str_starts_with($tmpPath, 'tmp/profiles/')) {
                $ext = pathinfo($tmpPath, PATHINFO_EXTENSION);
                $finalPath = 'profiles/' . Str::uuid() . ($ext ? ".{$ext}" : '');

                Storage::disk('public')->move($tmpPath, $finalPath);
                if ($user->profile_image) {
                    Storage::disk('public')->delete($user->profile_image);
                }
                $data['profile_image'] = $finalPath;
            } elseif (str_starts_with($tmpPath, 'profiles/')) {
                $data['profile_image'] = $tmpPath;
            } else {
                abort(422, 'Invalid temporary path');
            }
        }

        $user->update($data);
        return redirect('/mypage');
    }
}
