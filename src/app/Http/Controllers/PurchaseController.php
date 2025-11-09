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
use Stripe\Stripe;

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
            $item = Item::with('categories')->findOrFail($item_id);
            $comments = Comment::with('user')->where('item_id', $item_id)->get();

            return view('items/show', compact('item', 'comments', 'sold'));
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
            //"order_draft.{$item_id}.item" => $item,
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
        Stripe::setApiKey(config('services.stripe.secret'));

        $item = Item::findOrFail($orderData['item_id']);
        $email = User::findOrFail($orderData['user_id'])->email;
        $method = Order::$paymentCodes[$orderData['payment_method']];

        $params = [
            'mode' => 'payment',
            'line_items' => [[
                'price_data' => [
                    'currency' => 'jpy',
                    'unit_amount' => $item->price,
                    'product_data' => ['name' => $item->item_name],
                ],
                'quantity' => 1,
            ]],

            'success_url' => url('/payment/success'),
            'cancel_url' => url('/payment/cancel'),

            'payment_method_types' => [$method],
        ];

        if ($method === 'konbini') {

            if ($email) {
                $params['customer_email'] = $email;
            } else {
                $params['customer_creation'] = 'always';
            }
        }

        $session = \Stripe\Checkout\Session::create($params);

        return redirect()->away($session->url);
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
