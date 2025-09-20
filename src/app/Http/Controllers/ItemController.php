<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Item;
use App\Models\Condition;

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
        //$item = Item::findOrFail($item_id);
        $item = Item::with('categories')->findOrFail($item_id);
        //dd($item);
        return view('items/show', compact('item'));
    }
}
