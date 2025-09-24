@extends('layouts.app')
@section('css')
<link rel="stylesheet" href="{{ asset('css/items/index.css') }}">
@endsection
@section('title')
商品一覧画面
@endsection
@section('input')
<input type="text" class="header__search" placeholder="何をお探しですか？" />
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
<div class="items__menu">
    <a href="/">おすすめ</a>　　　　　<a href="/?tab=mylist" class="item__menu-link">マイリスト</a>
</div>
<hr />
<div class="items__content">

    @foreach($items as $item)
    @if(empty(auth()->id()) || (auth()->id() !== $item['user_id']))
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
    <div class="items__image">
        <a href="/item/{{ $item['id'] }}">
            <img src="{{ $currentImage }}" alt="商品画像" class="items__image-content" />
            <div>
                {{ $item['item_name'] }}
                @if($item->order)
                <span class="sold">Sold</span>
                @endif
            </div>
        </a>
    </div>
    @endif
    @endforeach
</div>
@endsection