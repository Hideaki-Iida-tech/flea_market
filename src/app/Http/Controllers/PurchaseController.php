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
    public function index($item_id)
    {
        if (Order::isSold($item_id)) {
            $item = Item::with('categories')->findOrFail($item_id);
            $comments = Comment::with('user')->where('item_id', $item_id)->get();
            $sold = Order::isSold($item_id);

            return view('items/show', compact('item', 'comments', 'sold'));
        }

        if (!User::isProfileCompleted(auth()->id())) {
            return redirect('/mypage/profile');
        }

        $draft = session("order_draft.{$item_id}", []);
        $item = Item::findOrFail($item_id);
        $paymentLabels = Order::$paymentLabels;
        $user = auth()->user();
        $sold = Order::isSold($item_id);
        $viewData = [
            'item' => $item,
            'user' => $user,
            'postal_code' => $draft['postal_code'] ?? $user->postal_code,
            'address' => $draft['address'] ?? $user->address,
            'building' => $draft['building'] ?? $user->building,
            'paymentLabels' => $paymentLabels,
            'sold' => $sold,
        ];

        return view('order.create', $viewData);
    }

    public function store(PurchaseRequest $request)
    {
        $validatedValue = $request->validated();
        $item_id = $validatedValue['item_id'];
        $sold = Order::isSold($item_id);

        if ($sold) {

            $draft = session("order_draft.{$item_id}", []);
            $item = Item::findOrFail($item_id);
            $paymentLabels = Order::$paymentLabels;
            $user = auth()->user();
            $sold = Order::isSold($item_id);
            $viewData = [
                'item' => $item,
                'user' => $user,
                'postal_code' => $draft['postal_code'] ?? $user->postal_code,
                'address' => $draft['address'] ?? $user->address,
                'building' => $draft['building'] ?? $user->building,
                'paymentLabels' => $paymentLabels,
                'sold' => $sold,
            ];

            return view('order.create', $viewData);
        }

        $postal_code = session(
            "order_draft.{$item_id}.postal_code",
            $validatedValue['postal_code']
        );
        $address = session(
            "order_draft.{$item_id}.address",
            $validatedValue['address']
        );
        $building = session(
            "order_draft.{$item_id}.building",
            $validatedValue['building']
        );

        $orderData = [
            'user_id' => auth()->id(),
            'item_id' => $item_id,
            'price' => $validatedValue['price'],
            'payment_method' => $validatedValue['payment_method'],
            'postal_code' => $postal_code,
            'address' => $address,
            'building' => $building,
        ];

        Order::create($orderData);
        session()->forget("order_draft.{$item_id}");

        // Stripeの決済画面の表示メソッドをコール
        return $this->createPayment($orderData);
    }

    public function addressIndex($item_id)
    {
        $sold = Order::isSold($item_id);

        if ($sold) {
            return redirect('/');
        }

        $draft = session("order_draft.{$item_id}", []);
        $item = Item::findOrFail($item_id);
        $user = auth()->user();
        $viewData = [
            'item' => $item,
            'postal_code' => $draft['postal_code'] ?? $user->postal_code,
            'address' => $draft['address'] ?? $user->address,
            'building' => $draft['building'] ?? $user->building,
        ];

        return view('address.edit', $viewData);
    }

    public function addressUpdate(AddressRequest $request)
    {
        $validatedValue = $request->validated();
        $item_id = $validatedValue['item_id'];
        $sold = Order::isSold($item_id);

        if ($sold) {
            return redirect('/');
        }

        $item = Item::findOrFail($item_id);
        session([
            "order_draft.{$item_id}.postal_code" => $validatedValue['postal_code'],
            "order_draft.{$item_id}.address" => $validatedValue['address'],
            "order_draft.{$item_id}.building" => $validatedValue['building'],
        ]);

        return redirect('/purchase/' . $item_id);
    }

    public function savePaymentDraft(PaymentDraftRequest $request)
    {
        $validatedValue = $request->validated();
        session(["order_draft.{$validatedValue['item_id']}.payment_method"
        => $validatedValue['payment_method']]);
        return response()->noContent(); //204
    }

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

    public function success(Request $request)
    {
        return view('auth.success');
    }

    public function cancel()
    {
        return route('auth.cancel');
    }
}
