<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\PurchaseRequest;
use App\Models\Item;
use App\Models\Order;
use App\Models\User;

class PurchaseController extends Controller
{

    //
    public function index($item_id)
    {
        if (Order::isSold($item_id)) {
            return back();
        }
        if (!User::isProfileCompleted(auth()->id())) {
            return redirect('/mypage/profile');
        }
        $item = Item::findOrFail($item_id);
        $paymentLabels = Order::$paymentLabels;
        $user = auth()->user();
        $sold = Order::isSold($item_id);
        return view('order.create', compact('item', 'paymentLabels', 'user', 'sold'));
    }

    public function store(PurchaseRequest $request)
    {

        $validatedValue = $request->validated();
        $sold = Order::isSold($validatedValue['item_id']);
        if ($sold) {
            return redirect('/');
        }
        Order::create($validatedValue);
        return redirect('/');
    }
}
