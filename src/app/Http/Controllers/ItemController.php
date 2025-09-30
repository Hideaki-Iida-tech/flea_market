<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Item;
use App\Models\Condition;
use App\Http\Requests\CommentRequest;
use App\Http\Requests\ExhibitionRequest;
use App\Models\Comment;
use App\Models\Order;
use App\Models\Category;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;


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

    public function create()
    {
        $categories = Category::all();
        $conditions = Condition::all();
        return view('items/create', compact('categories', 'conditions'));
    }
    public function store(ExhibitionRequest $request)
    {
        $data = $request->except('categories', 'current_item_image', 'item_image');
        $data['user_id'] = auth()->id();
        $categories = $request->input('categories', []);

        DB::beginTransaction();
        try {


            if ($request->hasFile('item_image')) {
                $path = $request->file('item_image')->store('items', 'public');
                $data['item_image'] = $path;
            }

            $item = Item::create($data);
            /*foreach ($categories as $key => $category) {
                DB::table('category_item')->insert([
                    'item_id' => $item->id,
                    'category_id' => $category,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }*/
            $item->categories()->attach($categories);

            DB::commit();
        } catch (\Exception $e) {
            DB::rollback();
            if ($data['item_image']) {
                Storage::disk('public')->delete($data['item_image']);
            }
            report($e);
            return back();
        }
        return redirect('/');
    }
}
