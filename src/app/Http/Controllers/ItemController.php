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
use Illuminate\Support\Facades\Log;

class ItemController extends Controller
{
    public function index(Request $request)
    {
        $mylist = $request->tab === 'mylist';
        $keyword = $request->input('keyword');

        if ($mylist) {
            $items = Item::with(['order', 'likes' => fn($query) => $query
                ->where('users.id', auth()->id())])
                ->whereHas(
                    'likes',
                    fn($query) => $query
                        ->where('users.id', auth()->id())
                );
        } else {
            $items = Item::with('order');
        }

        if ($keyword) {
            $items = $items->KeywordSearch($keyword);
        }

        $items = $items->get();

        $query = $request->only('keyword');
        $base = url('/');

        return view('items/index', compact(
            'items',
            'mylist',
            'keyword',
            'query',
            'base'
        ));
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
        $validatedValue = $request->validated();
        $item_id = $validatedValue['item_id'];
        $validatedValue['user_id'] = auth()->id();
        $validatedValue['item_id'] = $item_id;

        Comment::create($validatedValue);

        return redirect('/item/' . $item_id);
    }

    public function toggle($item_id, LikeRequest $request)
    {
        $validatedValue = $request->validated();
        $item_id = $validatedValue['item_id'];
        $user = $request->user();
        $item = Item::findOrFail($item_id);

        if ($item->likes()->where('user_id', $user->id)->exists()) {
            $item->likes()->detach($user->id);
        } else {
            $item->likes()->attach($user->id);
        }

        return back();
    }

    public function create()
    {
        // カテゴリマスタが未登録なら、config から投入
        if (!Category::query()->exists()) {
            $masterCategories = config('master.categories', []);

            foreach ($masterCategories as $attrs) {
                Category::create($attrs);
            }
        }

        // コンディションマスタが未登録なら、config から投入
        if (!Condition::query()->exists()) {
            $masterConditions = config('master.conditions', []);

            foreach ($masterConditions as $attrs) {
                Condition::create($attrs);
            }
        }

        // 改めて DB から取得（ここに来る時点で必ずDBにマスタデータが入っている想定）
        $categories = Category::all();
        $conditions = Condition::all();

        return view('items/create', compact('categories', 'conditions'));
    }

    public function store(ExhibitionRequest $request)
    {
        $data = $request->except('categories', 'current_item_image', 'item_image');
        $data['user_id'] = auth()->id();
        $categories = $request->input('categories', []);
        // hiddenから商品画像の一時パスを取得
        $tmpPath = $request->input('current_item_image');
        $finalPath = null;

        DB::beginTransaction();
        try {

            if ($tmpPath) {

                if (!str_starts_with($tmpPath, 'tmp/items/')) {
                    abort(422, 'Invalid temporary path');
                }

                $ext = pathinfo($tmpPath, PATHINFO_EXTENSION);
                $finalPath = 'items/' . Str::uuid() . ($ext ? ".{$ext}" : '');

                Storage::disk('public')->move($tmpPath, $finalPath);
                $data['item_image'] = $finalPath;
            }

            $item = Item::create($data);
            $item->categories()->attach($categories);

            DB::commit();

            return redirect('/sell/success');
        } catch (\Exception $e) {

            DB::rollback();

            // もしすでに items/ へ移動していたら掃除
            if ($finalPath && Storage::disk('public')->exists($finalPath)) {
                Storage::disk('public')->delete($finalPath);
            }

            report($e);
            //return back()->withInput();
            return redirect('/sell/error');
        }
    }

    public function success()
    {
        return view('items/success');
    }

    public function error()
    {
        return view('items/error');
    }
}
