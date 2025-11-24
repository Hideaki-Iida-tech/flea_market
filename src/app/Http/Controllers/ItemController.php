<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Item;
use App\Models\Condition;
use App\Http\Requests\CommentRequest;
use App\Http\Requests\ExhibitionRequest;
use App\Http\Requests\LikeRequest;
use App\Models\Comment;
use App\Models\Order;
use App\Models\Category;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ItemController extends Controller
{
    /**
     * 商品一覧情報を表示するアクションメソッド
     * 
     * マイリストタブ選択時：ログインユーザーがいいねした商品のみを表示
     * それ以外：全商品をおすすめ一覧として表示
     * キーワード指定時：商品名を部分一致検索で絞り込み
     * 
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Contracts\View\View
     */
    public function index(Request $request)
    {
        // マイリストがクリックされているかどうかを設定
        $mylist = $request->tab === 'mylist';
        // 検索ボックスに入力されている文字列を取得
        $keyword = $request->input('keyword');
        // マイリストページの場合
        if ($mylist) {
            // ログインユーザーがいいねした商品データを設定
            $items = Item::with(['order', 'likes' => fn($query) => $query
                ->where('users.id', auth()->id())])
                ->whereHas(
                    'likes',
                    fn($query) => $query
                        ->where('users.id', auth()->id())
                );
            // マイリストページではない場合（おすすめページ）
        } else {
            // 全商品情報を設定
            $items = Item::with('order');
        }

        // キーワードが入力されている場合
        if ($keyword) {
            // ローカルスコープ（Itemモデル内で定義した検索メソッド）を読み出し
            $items = $items->KeywordSearch($keyword);
        }
        // マイリスト／おすすめに応じたデータを取得
        $items = $items->get();
        // 検索ワードの入った配列を取得
        $query = $request->only('keyword');
        $base = url('/');

        // 取得したデータを渡して一覧のviewを表示
        return view('items/index', compact(
            'items',
            'mylist',
            'keyword',
            'query',
            'base'
        ));
    }

    /**
     * 商品詳細画面を表示するアクションメソッド
     * 
     * 指定された商品IDに対応する商品情報を取得し、
     * ・商品に紐づくカテゴリ
     * ・その商品のコメント一覧（ユーザー情報付き）
     * ・商品の販売済みステータス
     * を取得して詳細ページに渡す。
     * @param int $item_id 商品ID
     * @return \Illuminate\Contracts\View\view
     */
    public function show($item_id)
    {
        // item_id で指定された商品データをカテゴリデータとともに取得
        $item = Item::with('categories')->findOrFail($item_id);
        // item_id で指定された商品に対して投稿されたコメント一覧を取得
        $comments = Comment::with('user')->where('item_id', $item_id)->get();
        // item_idで指定された商品が購入済みかどうかを取得
        $sold = Order::isSold($item_id);

        // 取得した情報を渡し、商品詳細画面のviewを表示。
        return view('items/show', compact('item', 'comments', 'sold'));
    }

    /**
     * コメント機能を実行するアクションメソッド
     * 
     * @param 
     *      App\Http\Requests\CommentRequest $request 入力情報に施すフォームリクエスト,
     *      int $item_id 商品ID
     * @return 
     *      Illuminate\Http\RedirectResponse
     * 商品IDとコメント内容、ユーザーIDを取得し、コメントテーブル（comments）に書き込み
     * 商品詳細画面にリダイレクト
     */
    public function commentCreate(CommentRequest $request, $item_id)
    {
        // 商品IDとコメント内容に対してバリデーションを実施し、結果の値を変数に格納
        $validatedValue = $request->validated();
        // 現在のログインユーザーのIDを変数の配列に追加
        $validatedValue['user_id'] = auth()->id();
        // 商品ID、ログインユーザーID、コメント内容をcommentsテーブルに追加
        Comment::create($validatedValue);

        // 現在の商品IDを指定して、商品詳細画面にリダイレクト
        return redirect('/item/' . $item_id);
    }

    /**
     * いいね機能を実行するアクションメソッド
     * 
     * @param 
     *      App\Http\Requests\LikeRequest $request 商品IDにバリデーションを施すフォームリクエスト,
     *      int $item_id 商品ID
     * @return 
     *      Illuminate\Http\RedirectResponse
     * 
     */
    public function toggle($item_id, LikeRequest $request)
    {
        // 商品IDに対してバリデーションを実施し、結果の値を変数に格納
        $validatedValue = $request->validated();
        // 商品IDを取り出す
        $item_id = $validatedValue['item_id'];
        // 現在のログインユーザーのuserインスタンスを取得
        $user = $request->user();
        // 商品IDからitemインスタンスを取得
        $item = Item::findOrFail($item_id);

        // 該当する商品インスタンスにログインユーザーのレコードが存在する場合
        if ($item->likes()->where('user_id', $user->id)->exists()) {
            // レコード削除
            $item->likes()->detach($user->id);
            // 該当するレコードが存在しない場合
        } else {
            // レコード追加
            $item->likes()->attach($user->id);
        }

        // 商品詳細画面にリダイレクト
        return back();
    }

    /**
     * 商品出品画面を表示するアクションメソッド
     * 
     * カテゴリ、商品状態のマスタデータを取得し、
     * そのデータを渡して、商品出品画面を表示させる
     * @param なし
     * @return \Illuminate\Contracts\View\view
     */
    public function create()
    {
        // カテゴリマスタが未登録なら、config から投入
        if (!Category::query()->exists()) {
            $masterCategories = config('master.categories', []);

            // カテゴリテーブルにconfigからの投入値を保存
            foreach ($masterCategories as $attrs) {
                Category::create($attrs);
            }
        }

        // コンディションマスタが未登録なら、config から投入
        if (!Condition::query()->exists()) {
            $masterConditions = config('master.conditions', []);

            // コンディションテーブルにconfigからの投入値を保存
            foreach ($masterConditions as $attrs) {
                Condition::create($attrs);
            }
        }

        // 改めて DB から取得（ここに来る時点で必ずDBにマスタデータが入っている想定）
        $categories = Category::all();
        $conditions = Condition::all();

        // すでに商品画像が選択されている状態で出品画面がリダイレクトされようとしている場合
        // 画像の一時パスを取得
        $currentPath = session()->getOldInput('current_item_image'); // リダイレクト前のページのhiddenの設定値
        // 一時パスが存在する場合、公開パスに変換した値を設定し、空の場合はプレースフォルダ画面を設定（ブラウザプレビュー用）
        /** @var \Illuminate\Filesystem\FilesystemAdapter $storage */
        $storage = Storage::disk('public');
        $currentUrl = $currentPath ? $storage->url($currentPath) : 'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAIAAACQd1PeAAAADElEQVR4nGM4c+YMAATMAmU5mmUsAAAAAElFTkSuQmCC';

        // 取得、設定したすべでの値を渡して、商品出品画面のviewを表示
        return view('items/create', compact('categories', 'conditions', 'currentPath', 'currentUrl'));
    }

    /**
     * 商品出品処理を実行するアクションメソッド
     * 
     * ・入力出品情報を取得
     * ・バリデーション実施
     * ・画像の一時ファイルを本番用のディレクトリへ移動
     * ・すべての情報をitemsテーブルとcategory_itemテーブルに保存
     * @param
     *      App\Http\Requests\ExhibitionRequest $request 出品情報画面での入力値に対するフォームリクエスト,
     * @return
     *      Illuminate\Http\RedirectResponse
     *      正常終了時：成功画面
     *      エラー時：エラー画面
     */
    public function store(ExhibitionRequest $request)
    {
        // カテゴリー、設定されているリダイレクト前の画像一時パス、ダイアログで設定された画像3つ以外の値を取得
        $data = $request->except('categories', 'current_item_image', 'item_image');
        // 出品者のユーザーIDを設定
        $data['user_id'] = auth()->id();
        // 選択されたカテゴリを配列で取得
        $categories = $request->input('categories', []);
        // 商品画像の一時パスを取得（フォームリクエストでtmpディレクトリに一時保存された画像のパスか
        // バリデーションエラーでリダイレクトされる前に選択されていた画像のパス）
        $tmpPath = $request->input('current_item_image');
        // 画像の最終保存場所のパスを初期化
        $finalPath = null;

        // トランザクション開始
        DB::beginTransaction();
        try {

            // 画像の一時パスが設定されている場合
            if ($tmpPath) {

                // 画像の一時パスがtmp/items/ディレクトリ以下でない場合
                if (!str_starts_with($tmpPath, 'tmp/items/')) {
                    abort(422, 'Invalid temporary path'); // 処理を中止
                }

                // 一時パスから、画像ファイルの拡張子を取得
                $ext = pathinfo($tmpPath, PATHINFO_EXTENSION);
                // uuidを利用してitems/ディレクトリ内の最終保存場所のパスを生成
                $finalPath = 'items/' . Str::uuid() . ($ext ? ".{$ext}" : '');

                // 画像ファイルを一時保存場所から最終保存場所へ移動
                Storage::disk('public')->move($tmpPath, $finalPath);
                // DB保存用配列に最終保存場所のパスを設定
                $data['item_image'] = $finalPath;
            }

            // 出品情報をitemsテーブルとcategory_itemテーブルに保存
            $item = Item::create($data);
            $item->categories()->attach($categories);

            // トランザクションを確定
            DB::commit();

            // 成功画面へリダイレクト
            return redirect('/sell/success');
            // 例外発生時
        } catch (\Exception $e) {
            // DBをロールバック
            DB::rollback();

            // もしすでに items/ へ移動していたら掃除
            if ($finalPath && Storage::disk('public')->exists($finalPath)) {
                Storage::disk('public')->delete($finalPath);
            }

            report($e);
            // 失敗画面へリダイレクト
            return redirect('/sell/error');
        }
    }

    /**
     * 出品処理が成功した時に成功画面のviewを表示するアクションメソッド
     * 
     * @param なし
     * @return \Illuminate\Contracts\View\view
     */
    public function success()
    {
        return view('items/success');
    }

    /**
     * 出品処理が失敗された時に失敗画面のviewを表示するアクションメソッド
     * 
     * @param なし
     * @return \Illuminate\Contracts\View\view
     */
    public function error()
    {
        return view('items/error');
    }
}
