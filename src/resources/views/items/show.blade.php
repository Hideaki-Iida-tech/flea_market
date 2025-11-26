@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/items/show.css') }}">
@endsection

@section('title')
商品詳細画面
@endsection

@section('input')
<form id="search-form" action="/" method="get">
    <input type="text" id="search-box" name="keyword" class="header-search" placeholder="何をお探しですか？" value="{{ old('keyword',$keyword ?? '') }}" />
</form>
@endsection

@section('button')
@if(Auth::check())
<div class="header-button">
    <form action="/logout" method="post">
        @csrf
        <button class="header-button-logout">ログアウト</button>
    </form>
    <form action="/mypage" method="get">
        <button class="header-button-mypage">マイページ</button>
    </form>
    <form action="/sell" method="get">
        <button class="header-button-sell">出品</button>
    </form>
</div>
@else
<div class="header-button">
    <form action="/login" method="get">
        @csrf
        <button class="header-button-logout">ログイン</button>
    </form>
    <form action="/mypage" method="get">
        <button class="header-button-mypage">マイページ</button>
    </form>
    <form action="/sell" method="get">
        <button class="header-button-sell">出品</button>
    </form>
</div>
@endif
@endsection

@section('content')
<div class="items-content">

    <div class="items-image">
        <img src="{{ $item->image_url }}" alt="商品画像" class="items-img" />
    </div>

    <div class="items-detail">

        <h1 class="item-name">
            {{ $item->item_name }}
        </h1>

        <div class="brand">
            {{ $item->brand }}
        </div>

        <div class="price">
            ¥<span class="price-font">{{ number_format($item->price) }}</span>（税込）
        </div>

        <div class="icons">

            <div class="like">
                @if(Auth::check())
                <form action="/item/{{$item->id}}/like" method="post">
                    @csrf
                    <button type="submit" class="like-button">
                        @if($item->likes->contains(auth()->id()))
                        <img src="{{ asset('images/like_on.png') }}" alt="いいね" class="like-img">
                        @else
                        <img src="{{ asset('images/like_off.png') }}" alt="いいね" class="like-img">
                        @endif
                    </button>
                </form>
                @else
                <form id="like-form" action="/item/{{$item->id}}/like" method="post">
                    @csrf
                    <button type="submit" class="like-button">
                        @if($item->likes->contains(auth()->id()))
                        <img src="{{ asset('images/like_on.png') }}" alt="いいね" class="like-img">
                        @else
                        <img src="{{ asset('images/like_off.png') }}" alt="いいね" class="like-img">
                        @endif
                    </button>
                </form>
                @endif
                <div class="like-count">
                    {{ $item->likes->count() }}
                </div>
            </div>

            <div class="comment">
                <img src="{{ asset('images/comment.png') }}" alt="コメント" class="comment-img">
                <div class="comment-count">
                    {{ $comments->count() }}
                </div>
            </div>

        </div>

        @if(!$sold)
        @if(Auth::check())
        <form action="/purchase/{{ $item->id }}" class="form-purchase" method="get">
            <button class="form-purchase-submit">購入手続きへ</button>
        </form>
        @else
        <form id="purchase-form" action="/purchase/{{ $item->id }}" class="form-purchase" method="get">
            <button class="form-purchase-submit">購入手続きへ</button>
        </form>
        @endif
        @else
        <div class="purchase-completed">購入処理済み</div>
        @endif

        <h2>商品説明</h2>

        <div class="description">
            {{ $item->description }}
        </div>

        <h2>商品の情報</h2>
        <table>

            <tr>
                <th class="col-title">
                    カテゴリー
                </th>
                <td class="categories-box">
                    @foreach($item->categories as $category)
                    <div class="category-name">
                        {{ $category->name }}
                    </div>
                    @endforeach
                </td>
            </tr>

            <tr>
                <th class="col-title">
                    商品の状態
                </th>
                <td class="condition-box">
                    <div class="condition-name">
                        {{ $item->condition->name }}
                    </div>
                </td>
            </tr>

        </table>

        <h2>コメント(<span class="comment-count">{{ $comments->count() }}</span>)</h2>
        <div class="comment-container">
            @foreach($comments as $comment)
            <div class="user-info">
                <img src="{{ $comment->user->profile_image_url }}" alt="プロフィールイメージ" class="profile-image">
                <div class="user-name">{{ $comment->user->name }}</div>
            </div>
            <div class="comment-content">{{ $comment->body }}</div>
            @endforeach
        </div>
        @if(Auth::check())
        <form action="/item/{{ $item->id }}/comment" class="comment-edit" method="post">
            @csrf
            <h3>商品へのコメント</h3>
            <textarea name="body" id="" class="comment-body">{{ old('body')}}</textarea>
            @if ($errors->has('body'))
            <div class="comment-alert-danger">
                <ul>
                    @foreach ($errors->get('body') as $error)
                    <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
            @endif

            <button class="comment-edit-submit">
                コメントを送信する
            </button>
        </form>
        @else
        <form id="comment-form" action="/item/{{ $item->id }}/comment" class="comment-edit" method="post">
            @csrf
            <h3>商品へのコメント</h3>
            <textarea name="body" id="" class="comment-body">{{ old('body')}}</textarea>
            @if ($errors->has('body'))
            <div class="comment-alert-danger">
                <ul>
                    @foreach ($errors->get('body') as $error)
                    <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
            @endif

            <button class="comment-edit-submit">
                コメントを送信する
            </button>
        </form>
        @endif
    </div>

</div>

<script>
    // Enterキー押下でフォームが自動送信されてしまうのを防ぎ、
    // 手動で search-form を送信するためのイベントリスナー
    // 検索ボックスに「商品名」が入力されてEnterキーが押下された場合の挙動
    document.getElementById('search-box').addEventListener('keydown', function(e) {
        // Enterキーが押された場合
        if (e.key === 'Enter') {
            e.preventDefault(); // デフォルトのフォーム送信をキャンセル
            document.getElementById('search-form').submit(); // 明示的にフォーム送信
        }
    });

    // 未ログイン状態での「いいね」「コメント」「購入」ボタン押下時に
    // アラート表示でユーザーにログインを促すための処理

    // 各フォーム要素を取得
    const likeForm = document.getElementById('like-form');
    const commentForm = document.getElementById('comment-form');
    const purchaseForm = document.getElementById('purchase-form');

    // いいねフォーム送信時のアラート表示
    likeForm.addEventListener("submit", function() {
        alert("いいねするにはログインが必要です。");
    });
    // コメントフォーム送信時のアラート表示
    commentForm.addEventListener("submit", function() {
        alert("コメントするにはログインが必要です。");
    });
    // 購入フォーム送信時のアラート表示
    purchaseForm.addEventListener("submit", function() {
        alert("購入手続きを行うにはログインが必要です。");
    });
</script>
@endsection