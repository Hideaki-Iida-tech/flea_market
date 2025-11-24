<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\ProfileRequest;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use App\Models\Item;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class ProfileController extends Controller
{
    /**
     * プロフィール画面を表示するアクションメソッド
     * 
     * デフォルト/出品した商品/購入した商品に分けて、表示する
     * デフォルトの場合は「出品した商品」と同内容を表示
     * @param Illuminate\Http\Request $request 通常のHTTPリクエストクラス
     * @return \Illuminate\Contracts\View\view
     */
    public function index(Request $request)
    {
        // クエリパラメータpageの値がsellの場合とデフォルトの場合、$isSellフラグをtrue
        $isSell = $request->page === 'sell' || !isset($request->page);
        // クエリパラメータpageの値がbuyの場合、$isBuyフラグをtrue
        $isBuy = $request->page === 'buy';
        // プロフィール画面に渡すitemsコレクションを初期化
        $items = null;

        // デフォルトと「出品した商品」の場合
        if ($isSell) {
            // itemsテーブルから出品者が自分の商品のみを、ordersテーブル（購入情報）と紐づけて取得
            $items = Item::with('order')->where('user_id', auth()->id())->get();
            // 「購入した商品」の場合
        } elseif ($isBuy) {
            // itemsテーブルとordersテーブル（購入情報）紐づけ、自分が購入した商品のみを取得
            $items = Item::whereHas('order', fn($query) => $query
                ->where('user_id', auth()->id()))
                ->with(['order' => fn($query) => $query
                    ->where('user_id', auth()->id())])->get();
        }

        // usersテーブルに登録されている自分のプロフィール画像のパスを取得し、公開URLに変換
        $currentImage = optional(auth()->user())->profile_image
            ? asset('storage/' . optional(auth()->user())->profile_image)
            : '';

        // 取得した情報を渡して、プロフィール画面のviewを表示
        return view('profile.index', compact('items', 'currentImage'));
    }

    /**
     * プロフィール編集画面の表示アクションメソッド
     * 
     * @param なし
     * @return \Illuminate\Contracts\View\view
     * 
     */
    public function edit()
    {
        // プロフィール編集画面でプロフィール画像が選択され、バリデーションエラーでリダイレクトされ場合はその画像の一時パスを、
        // 一時パスが設定されていない場合や、プロフィール画面から遷移してきた場合には、DBに保存されている画像のパスを設定。
        $currentPath = session()->getOldInput('current_profile_image', optional(auth()->user())->profile_image);

        // パスを取得できた場合は、画像のパスからプレビュー表示用の公開URLを生成、取得的できなければ、プレースホルダ画像を設定
        /** @var \Illuminate\Filesystem\FilesystemAdapter $storage */
        $storage = Storage::disk('public');
        $currentUrl = $currentPath ? $storage->url($currentPath) : 'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAIAAACQd1PeAAAADElEQVR4nGM4c+YMAATMAmU5mmUsAAAAAElFTkSuQmCC';

        // 取得・設定した値を渡して、プロフィール編集画面のviewを表示
        return view('profile.edit', compact('currentPath', 'currentUrl'));
    }

    /**
     *  プロフィールの更新アクションメソッド
     * 
     * @param 
     *      App\Http\Requests\ProfileRequest $request プロフィール変更画面での入力情報
     *      をバリデートするフォームリクエストクラス
     * @return
     *      Illuminate\Http\RedirectResponse
     */
    public function update(ProfileRequest $request)
    {

        // 現在ログイン中のuserインスタンスを取得
        $user = User::findOrFail(Auth::id());
        // テキスト項目と編集完了フラグを変数に格納
        $data = $request->only([
            'name',
            'postal_code',
            'address',
            'building',
            'is_profile_completed'
        ]);

        // バリデーションエラー発生時にhiddenで保持された値 か 
        // fileダイアログで選択した画像のパス（フォームリクエストでマージ）を取得
        $tmpPath = $request->input('current_profile_image');
        // 最終保存先のパスを初期化
        $finalPath = null;

        // プロフィール画像のパスが存在する場合
        if ($tmpPath) {

            // 画像が一時ファイルの場合
            if (str_starts_with($tmpPath, 'tmp/profiles/')) {
                // ファイルの拡張子を取得
                $ext = pathinfo($tmpPath, PATHINFO_EXTENSION);
                // uuidを使って最終保存先のパスを確定
                $finalPath = 'profiles/' . Str::uuid() . ($ext ? ".{$ext}" : '');

                // 一時ファイルを最終保存先に移動
                Storage::disk('public')->move($tmpPath, $finalPath);

                // 更新前の画像ファイルが最終保存先に存在する場合
                if ($user->profile_image) {
                    Storage::disk('public')->delete($user->profile_image); // 更新前の画像ファイル削除
                }

                // DB保存用変数に最終保存先のパスを設定
                $data['profile_image'] = $finalPath;
                // パスが最終保存先のディレクトリの場合
            } elseif (str_starts_with($tmpPath, 'profiles/')) {
                // そのまま、パスをDB保存用変数に設定
                $data['profile_image'] = $tmpPath;
                // それ以外の場合
            } else {
                // 処理を中止
                abort(422, 'Invalid temporary path');
            }
        }

        // 設定値でDB更新
        $user->update($data);

        // プロフィール画面へリダイレクト
        return redirect('/mypage');
    }
}
