<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\PurchaseRequest;
use App\Http\Requests\AddressRequest;
use App\Http\Requests\PaymentDraftRequest;
use App\Models\Item;
use App\Models\Order;
use App\Models\User;

class PurchaseController extends Controller
{

    //
    public function index($item_id)
    {
        if (Order::isSold($item_id)) {
            return redirect('/');
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
        //return view('order.create', compact('item', 'paymentLabels', 'user', 'sold'));
        return view('order.create', $viewData);
    }

    public function store(PurchaseRequest $request)
    {

        $validatedValue = $request->validated();
        $item_id = $validatedValue['item_id'];
        $sold = Order::isSold($item_id);
        if ($sold) {
            return redirect('/');
        }

        $postal_code = session("order_draft.{$item_id}.postal_code", $validatedValue['postal_code']);
        $address = session("order_draft.{$item_id}.address", $validatedValue['address']);
        $building = session("order_draft.{$item_id}.building", $validatedValue['building']);

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
        return redirect('/');
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
        session(["order_draft.{$validatedValue['item_id']}.payment_method" => $validatedValue['payment_method']]);
        return response()->noContent(); //204
    }
}
