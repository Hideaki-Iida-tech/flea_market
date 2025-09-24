<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Item;
use App\Models\Condition;
use App\Http\Requests\CommentRequest;
use App\Models\Comment;
use App\Models\Order;

class ItemController extends Controller
{
    //
    public function index()
    {
        $items = Item::with('order')->get();
        return view('items/index', compact('items'));
    }
    public function show($item_id)
    {
        $item = Item::with('categories')->findOrFail($item_id);
        $comments = Comment::with('user')->where('item_id', $item_id)->get();
        $sold = Order::isSold($item_id);
        //dd($item);
        return view('items/show', compact('item', 'comments', 'sold'));
    }
    public function commentCreate(CommentRequest $request, $item_id)
    {
        $form = $request->all();
        $form['user_id'] = auth()->id();
        $form['item_id'] = $item_id;
        Comment::create($form);
        return redirect('/item/' . $item_id);
    }
    public function toggle($item_id, Request $request)
    {
        $user = $request->user();
        $item = Item::find($item_id);
        if ($item->likes()->where('user_id', $user->id)->exists()) {
            $item->likes()->detach($user->id);
        } else {
            $item->likes()->attach($user->id);
        }
        return back();
    }
}
