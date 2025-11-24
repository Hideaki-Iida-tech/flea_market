<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\PurchaseRequest;
use App\Http\Requests\AddressRequest;
use App\Http\Requests\PaymentDraftRequest;
use App\Models\Item;
use App\Models\Order;
use App\Models\User;
use App\Models\Comment;
use Illuminate\Support\Facades\Log;

class PurchaseController extends Controller
{

    /**
     * 商品購入画面を表示するアクションメソッド
     * 
     * ・パスパラメータ$item_idで指定した商品が既に購入済みの場合は、商品詳細画面に戻る
     * ・ログイン中のユーザーのプロフィールに未設定項目がある場合は、プロフィール編集画面にリダイレクト
     * ・どちらでもない場合にパスパラメータ$item_idで指定した商品についての購入画面を表示
     * @param int $item_id 商品ID
     * @return \Illuminate\Contracts\View\view | Illuminate\Http\RedirectResponse
     */
    public function index($item_id)
    {

        // 該当する$item_idの商品が購入済みかどうかのフラグ値を設定
        $sold = Order::isSold($item_id);
        // パスパラメータで指定した$item_idの商品が購入済みの場合
        if ($sold) {
            // itemsテーブルから該当の$item_idのレコード（カテゴリと紐づけたもの）を取得
            $item = Item::with('categories')->findOrFail($item_id);
            // commentsテーブルから該当の$item_idのレコード（usersテーブルと紐づけたもの）を取得
            $comments = Comment::with('user')->where('item_id', $item_id)->get();

            // 取得・設定したすべての値を渡して、商品詳細画面のviewを表示
            return view('items/show', compact('item', 'comments', 'sold'));
        }

        // ログイン中のユーザーのプロフィールの必須項目に未設定項目がある場合
        if (!User::isProfileCompleted(auth()->id())) {
            // プロフィール編集画面へリダイレクト
            return redirect('/mypage/profile');
        }

        // 未購入で、プロフィール情報がすべて設定済みの場合、以下の通り処理続行
        // 「商品納入画面」で支払い方法を選択した際と、
        // 「送付先住所変更画面」で送付先を更新した際に保存されたセッションを取得
        $draft = session("order_draft.{$item_id}", []);
        // 該当の$item_idのレコードのインスタンスを取得
        $item = Item::findOrFail($item_id);
        // 商品購入画面のセレクトボックスへの表示用に「コンビニ払い」「カード払い」
        // と対応する整数値コードの連想配列を取得
        $paymentLabels = Order::$paymentLabels;
        // ログイン中のuserモデルのインスタンスを取得
        $user = auth()->user();
        // 商品購入画面への表示用の情報を生成

        // 前の画面で選択された支払い方法の整数コードをセッションから読み出し
        // null のときは '' に倒す（未選択扱いを安定化）
        $selectedPayment = session()->getOldInput(
            'payment_method',
            session("order_draft.{$item->id}.payment_method")
        ) ?? '';

        $viewData = [
            'item' => $item,
            'user' => $user,
            // 郵便番号 セッションから取得。取得できなければ、ログインユーザーのDBから取得
            'postal_code' => $draft['postal_code'] ?? $user->postal_code,
            // 住所 セッションから取得。取得できなければ、ログインユーザーのDBから取得
            'address' => $draft['address'] ?? $user->address,
            // 建物名 セッションから取得。取得できなければ、ログインユーザーのDBから取得
            'building' => $draft['building'] ?? $user->building,
            // 支払い方法選択のセレクトボックスに表示するラベル
            'paymentLabels' => $paymentLabels,
            // 選択されていた支払い方法の整数コードを復元
            'selectedPayment' => $selectedPayment,
            'sold' => $sold,
        ];

        // 設定した情報を渡して、商品購入画面のviewを表示
        return view('order.create', $viewData);
    }

    /**
     * 商品購入機能のアクションメソッド
     * 
     * ・パスパラメータ$item_idで指定した商品が購入済みの場合、
     *      同じ画面（商品購入画面）のviewを表示
     * ・購入済みではない場合、購入済みを表すordersテーブルを更新して、
     *      createPayment（Stripe関係の処理をまとめたメソッド）をコール
     * @param App\Http\Requests\PurchaseRequest $request
     * @return \Illuminate\Contracts\View\view | Illuminate\Http\RedirectResponse
     */
    public function store(PurchaseRequest $request)
    {
        // 商品購入画面から送信された各hiddenフィールドの値及び、
        // FormRequestで差し込まれたパスパラメータ$item_idと、ログインユーザーのidに対して
        // バリデーションを実行し、変数$validatedValueに格納
        $validatedValue = $request->validated();
        // item_idを取得
        $item_id = $validatedValue['item_id'];
        // 該当する$item_idの商品が購入済みかどうかをフラグに格納
        $sold = Order::isSold($item_id);

        // 購入済みの場合
        if ($sold) {

            // セッションからを読み込み
            $draft = session("order_draft.{$item_id}", []);
            // 該当する商品のモデルインスタンスを取得
            $item = Item::findOrFail($item_id);
            // 商品購入画面のセレクトボックスへの表示用に「コンビニ払い」「カード払い」
            // と対応する整数値コードの連想配列を取得
            $paymentLabels = Order::$paymentLabels;
            // ログイン中のユーザーのモデルインスタンスを取得
            $user = auth()->user();

            // 前の画面で選択された支払い方法の整数コードをセッションから読み出し
            // null のときは '' に倒す（未選択扱いを安定化）
            $selectedPayment = session()->getOldInput(
                'payment_method',
                session("order_draft.{$item->id}.payment_method")
            ) ?? '';

            // 商品購入画面のviewを再表示するための情報を作成
            $viewData = [
                'item' => $item,
                'user' => $user,
                // 郵便番号 セッションから取得。取得できなければ、ログインユーザーのDBから取得
                'postal_code' => $draft['postal_code'] ?? $user->postal_code,
                // 住所 セッションから取得。取得できなければ、ログインユーザーのDBから取得
                'address' => $draft['address'] ?? $user->address,
                // 建物名 セッションから取得。取得できなければ、ログインユーザーのDBから取得
                'building' => $draft['building'] ?? $user->building,
                // 支払い方法選択のセレクトボックスに表示するラベル
                'paymentLabels' => $paymentLabels,
                // 選択されていた支払い方法の整数コードを復元
                'selectedPayment' => $selectedPayment,
                'sold' => $sold,
            ];

            // 取得・設定した値を渡して、商品購入画面のviewを再表示
            return view('order.create', $viewData);
        }

        // 購入済みでない場合、以下の処理を続行
        // 郵便番号をセッションから取得　セッションに値がなければ、
        // 商品購入画面のhiddenフィールドからバリデーションしたものを取得
        $postal_code = session(
            "order_draft.{$item_id}.postal_code",
            $validatedValue['postal_code']
        );
        // 住所をセッションから取得　セッションに値がなければ、
        // 商品購入画面のhiddenフィールドからバリデーションしたものを取得
        $address = session(
            "order_draft.{$item_id}.address",
            $validatedValue['address']
        );
        // 建物名をセッションから取得　セッションに値がなければ、
        // 商品購入画面のhiddenフィールドからバリデーションしたものを取得
        $building = session(
            "order_draft.{$item_id}.building",
            $validatedValue['building']
        );

        // ordersテーブルに登録する情報を設定
        $orderData = [
            'user_id' => auth()->id(),
            'item_id' => $item_id,
            'price' => $validatedValue['price'],
            'payment_method' => $validatedValue['payment_method'],
            'postal_code' => $postal_code,
            'address' => $address,
            'building' => $building,
        ];

        // ordersテーブルに購入情報を保存
        Order::create($orderData);
        // セッションを解放
        session()->forget("order_draft.{$item_id}");

        // Stripeの決済画面の表示メソッドをコール
        return $this->createPayment($orderData);
    }

    /**
     * 送付先住所変更画面のviewを表示するアクションメソッド
     * 
     * ・パスパラメータ$item_idの商品が購入済みの場合、商品一覧画面にリダイレクト
     * ・それ以外の場合、送付先住所変更画面のviewを表示
     * @param int $item_id 商品ID
     * @return Illuminate\Http\RedirectResponse | \Illuminate\Contracts\View\view
     */
    public function addressIndex($item_id)
    {
        // パスパラメータ$item_idの商品が購入済みであるかどうかをフラグに設定
        $sold = Order::isSold($item_id);

        // 購入済みの場合
        if ($sold) {
            // 商品一覧画面にリダイレクト
            return redirect('/');
        }

        // $item_idに該当するセッションの値を読み込み
        $draft = session("order_draft.{$item_id}", []);
        // $item_idに該当する商品のモデルインスタンスを取得
        $item = Item::findOrFail($item_id);
        // 現在ログイン中のユーザーのモデルインスタンスを取得
        $user = auth()->user();
        // 送付先住所変更画面に渡す値を設定
        $viewData = [
            'item' => $item,
            // 郵便番号 セッションから取得。取得できなければ、ログインユーザーのDBから取得
            'postal_code' => $draft['postal_code'] ?? $user->postal_code,
            // 住所 セッションから取得。取得できなければ、ログインユーザーのDBから取得
            'address' => $draft['address'] ?? $user->address,
            // 建物名 セッションから取得。取得できなければ、ログインユーザーのDBから取得
            'building' => $draft['building'] ?? $user->building,
        ];

        // 設定した値を渡して、送付先住所変更画面のviewを表示
        return view('address.edit', $viewData);
    }

    /**
     * 送付先住所変更画面からのデータ更新処理のアクションメソッド
     * 
     * ・パスパラメータ$item_idの商品が購入済みの場合は商品一覧画面にリダイレクト
     * ・それ以外の場合は、入力値「郵便番号」「住所」「建物名」をセッションに保存して
     *      商品購入画面にリダイレクト
     * @param App\Http\Requests\AddressRequest $request
     * @return Illuminate\Http\RedirectResponse
     */
    public function addressUpdate(AddressRequest $request)
    {
        // 入力値にバリデーションを実施して変数に格納
        $validatedValue = $request->validated();
        // パスパラメータ由来の$item_idを取得
        $item_id = $validatedValue['item_id'];
        // $item_idに該当する商品が購入済みかどうかをフラグに格納
        $sold = Order::isSold($item_id);

        // 購入済みの場合
        if ($sold) {
            // 商品一覧画面にリダイレクト
            return redirect('/');
        }

        // $item_idに該当する商品のモデルインスタンスを取得
        $item = Item::findOrFail($item_id);
        // 入力値「郵便番号」「住所」「建物名」をセッションに保存
        session([
            "order_draft.{$item_id}.postal_code" => $validatedValue['postal_code'],
            "order_draft.{$item_id}.address" => $validatedValue['address'],
            "order_draft.{$item_id}.building" => $validatedValue['building'],
        ]);

        // 商品購入画面にリダイレクト
        return redirect('/purchase/' . $item_id);
    }

    /**
     * 商品購入画面の支払い方法を選択するセレクトボックスの値が変更された時に
     * 選択した値をセッションに保存するアクションメソッド
     * @param \App\Http\Requests\PaymentDraftRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function savePaymentDraft(PaymentDraftRequest $request)
    {
        // item_idについてバリデーションを実行して変数に格納
        $validatedValue = $request->validated();
        // 選択された支払い方法の整数キーをセッションに保存
        session(["order_draft.{$validatedValue['item_id']}.payment_method"
        => $validatedValue['payment_method']]);
        // 内容なしを返す
        return response()->noContent(); //204
    }

    /**
     * Stripe決済機能関係をまとめたメソッド
     * 
     * storeメソッドからコールされる。
     * @param array $orderData 商品購入情報
     * @return Illuminate\Http\RedirectResponse
     */
    private function createPayment(array $orderData)
    {
        // ライブラリは使わず、HTTPで直接Stripeを叩く版
        $apiKey = config('services.stripe.secret');

        $item = Item::findOrFail($orderData['item_id']);
        $user = User::findOrFail($orderData['user_id']);
        $email = $user->email;
        $method = Order::$paymentCodes[$orderData['payment_method']];

        // Checkout Session作成パラメータ
        $params = [
            'mode' => 'payment',
            // 成功/キャンセル後に便利なように session_id をクエリで返してもらう
            'success_url' => url('/payment/success')
                . '?session_id={CHECKOUT_SESSION_ID}',
            'cancel_url' => url('/payment/cancel')
                . '?session_id={CHECKOUT_SESSION_ID}',

            // 支払い手段を明示（例：['card'] や ['konbini']）
            'payment_method_types' => [$method],

            // line_items はネスト配列（http_bulid_query で form-encodedになる）
            'line_items' => [[
                'price_data' => [
                    'currency' => 'jpy',
                    // 金額は「サーバーで信頼できる値」を使う
                    'unit_amount' => $item->price,
                    'product_data' => [
                        'name' => $item->item_name,
                    ],
                ],
                'quantity' => 1,
            ]],
        ];

        // コンビニ払いのときの追加（任意：Eメールがあれば事前に紐づけ）
        if ($method === 'konbini') {
            if (!empty($email)) {
                $params['customer_email'] = $email;
            } else {
                $params['customer_creation'] = 'always';
            }
        }

        // Stripe API 呼出（Checkout Session作成）
        $ch = curl_init('https://api.stripe.com/v1/checkout/sessions');
        curl_setopt_array($ch, [
            CURLOPT_POST => true,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT => 30,
            // Stripeは form-encoded を標準サポート（JSONでも可だが、確実さ優先）
            CURLOPT_HTTPHEADER => [
                'Authorization: Bearer ' . $apiKey,
                'Content-Type: application/x-www-form-urlencoded',
                // 必要に応じてAPIバージョンを固定
                // 'Stripe-Version: 2023-10-16'
            ],
            // ネスト配列は http_build_query で "line_items[0][price_data][currency]=jpy"の形に
            CURLOPT_POSTFIELDS => http_build_query($params),
        ]);

        $raw = curl_exec($ch);
        $errno = curl_errno($ch);
        $error = curl_error($ch);
        $status = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($errno) {
            // ネットワークエラー
            Log::error('Stripe API network error', ['errno' => $errno, 'error' => $error]);
            abort(502, 'Payment gateway unreachable');
        }

        $data = json_decode($raw, true);

        if ($status < 200 || $status >= 300 || !is_array($data)) {
            // Stripeからのエラーレスポンス
            Log::error('Stripe API error', ['status' => $status, 'response' => $raw]);
            abort(500, 'Payment initialization failed');
        }

        if (empty($data['url'])) {
            Log::error('Stripe API: no session url', ['response' => $data]);
            abort(500, 'Payment session URL not provided');
        }

        // Checkoutページへリダイレクト（PRG維持）
        return redirect()->away($data['url']);
    }

    /**
     * 決済成功時のコールバック処理を行い、完了画面を表示する。
     *
     * Stripe Checkout からリダイレクトされた際に呼び出され、
     * 成功用のビュー（auth.success）を返す。
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Contracts\View\View
     */
    public function success(Request $request)
    {
        return view('auth.success');
    }

    /**
     * 決済がキャンセルされた際の遷移先URLを返す。
     *
     * Stripe Checkout からキャンセル時にリダイレクトされた後、
     * アプリ内のキャンセル処理用ルート（auth.cancel）へのURLを返却する。
     *
     * @return string  決済キャンセル画面へのURL
     */
    public function cancel()
    {
        return route('auth.cancel');
    }
}
