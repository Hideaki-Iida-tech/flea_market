@extends('layouts.app')
@section('css')
<link rel="stylesheet" href="{{ asset('css/items/show.css') }}">
@endsection
@section('title')
商品詳細画面
@endsection
@section('input')
<form id="search-form" action="/" method="get">
    <input type="text" id="search-box" name="keyword" class="header__search" placeholder="何をお探しですか？" value="{{ old('keyword',$keyword ?? '') }}" />
</form>
<script>
    document.getElementById('search-box').addEventListener('keydown', function(e) {
        if (e.key === 'Enter') {
            e.preventDefault();
            document.getElementById('search-form').submit();
        }
    });
</script>
@endsection
@section('button')
@if(Auth::check())
<div class="header__button">
    <form action="/logout" method="post">
        @csrf
        <button class="header__button-logout">ログアウト</button>
    </form>
    <form action="/mypage" method="get">
        <button class="header__button-mypage">マイページ</button>
    </form>
    <form action="/sell" method="get">
        <button class="header__button-sell">出品</button>
    </form>
</div>
@else
<div class="header__button">
    <form action="/login" method="get">
        @csrf
        <button class="header__button-logout">ログイン</button>
    </form>
    <form action="/mypage" method="get">
        <button class="header__button-mypage">マイページ</button>
    </form>
    <form action="/sell" method="get">
        <button class="header__button-sell">出品</button>
    </form>
</div>
@endif
@endsection
@section('content')
<div class="items__content">
    <div class="items__image">
        @php
        if(str_starts_with($item['item_image'],'https://')){
        $currentImage = $item['item_image'];
        }
        elseif($item['item_image']){
        $currentImage = asset('storage/' . $item['item_image']);
        }else{
        $currentImage = '';
        }
        @endphp
        <img src="{{ $currentImage }}" alt="商品画像" class="items__img" />
    </div>
    <div class="items__detail">
        <h1 class="item__name">
            {{ $item['item_name'] }}
        </h1>
        <div class="brand">
            {{ $item['brand'] }}
        </div>
        <div class="price">
            ¥<span class="price__font">{{ number_format($item['price']) }}</span>（税込）
        </div>
        <div class="icons">


            <div class="like">
                @if(Auth::check())
                <form action="/item/{{$item['id']}}/like" method="post">
                    @csrf
                    <button type="submit" class="like-button">
                        @if($item->likes->contains(auth()->id()))
                        <img src="{{ asset('images/like_on.png') }}" alt="いいね" class="like__img">
                        @else
                        <img src="{{ asset('images/like_off.png') }}" alt="いいね" class="like__img">
                        @endif
                    </button>
                </form>
                @else
                <form id="like-form" action="/item/{{$item['id']}}/like" method="post">
                    @csrf
                    <button type="submit" class="like-button">
                        @if($item->likes->contains(auth()->id()))
                        <img src="{{ asset('images/like_on.png') }}" alt="いいね" class="like__img">
                        @else
                        <img src="{{ asset('images/like_off.png') }}" alt="いいね" class="like__img">
                        @endif
                    </button>
                </form>
                @endif
                <div class="like__count">
                    {{ $item->likes->count() }}
                </div>
            </div>

            <div class="comment">
                <img src="{{ asset('images/comment.png') }}" alt="コメント" class="comment__img">
                <div class="comment__count">
                    {{ $comments->count() }}
                </div>
            </div>
        </div>
        @if(!$sold)
        @if(Auth::check())
        <form action="/purchase/{{ $item['id'] }}" class="form__purchase" method="get">
            <button class="form__purchase-submit">購入手続きへ</button>
        </form>
        @else
        <form id="purchase-form" action="/purchase/{{ $item['id'] }}" class="form__purchase" method="get">
            <button class="form__purchase-submit">購入手続きへ</button>
        </form>
        @endif
        @endif
        <h2>商品説明</h2>
        <div class="description">
            {{ $item['description'] }}
        </div>
        <h2>商品の情報</h2>
        <table>
            <tr>
                <th>
                    カテゴリー
                </th>
                <td class="categories__box">
                    @foreach($item->categories as $category)
                    <div class="category__name">
                        {{ $category->name }}
                    </div>
                    @endforeach
                </td>
            </tr>
            <tr>
                <th>
                    商品の状態
                </th>
                <td class="condition__box">
                    <div class="condition__name">
                        {{ $item->condition->name }}
                    </div>
                </td>
            </tr>
        </table>
        <h2>コメント(<span class="comment__count">{{ $comments->count() }}</span>)</h2>
        <div class="comment__container">
            @foreach($comments as $comment)
            @php
            $currentImage = optional($comment->user)->profile_image ? asset('storage/' . $comment->user->profile_image) : '';
            @endphp
            <div class="user__info">
                <img src="{{ $currentImage }}" alt="プロフィールイメージ" class="profile__image">
                <div class="user__name">{{ $comment->user->name }}</div>
            </div>
            <div class="comment__content">{{ $comment->body }}</div>
            @endforeach
        </div>
        @if(Auth::check())
        <form action="/item/{{ $item['id'] }}/comment" class="comment__edit" method="post">
            @csrf
            <h3>商品へのコメント</h3>
            <textarea name="body" id="" class="comment__body">{{ old('body')}}</textarea>
            @if ($errors->has('body'))
            <div class="comment__alert-danger">
                <ul>
                    @foreach ($errors->get('body') as $error)
                    <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
            @endif

            <button class="comment__edit-submit">
                コメントを送信する
            </button>
        </form>
        @else
        <form id="comment-form" action="/item/{{ $item['id'] }}/comment" class="comment__edit" method="post">
            @csrf
            <h3>商品へのコメント</h3>
            <textarea name="body" id="" class="comment__body">{{ old('body')}}</textarea>
            @if ($errors->has('body'))
            <div class="comment__alert-danger">
                <ul>
                    @foreach ($errors->get('body') as $error)
                    <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
            @endif

            <button class="comment__edit-submit">
                コメントを送信する
            </button>
        </form>
        @endif
    </div>
</div>
<script>
    const likeForm = document.getElementById('like-form');
    const commentForm = document.getElementById('comment-form');
    const purchaseForm = document.getElementById('purchase-form');
    likeForm.addEventListener("submit", function() {
        alert("いいねするにはログインが必要です。");
    });
    commentForm.addEventListener("submit", function() {
        alert("コメントするにはログインが必要です。");
    });
    purchaseForm.addEventListener("submit", function() {
        alert("購入手続きを行うにはログインが必要です。");
    });
</script>
@endsection