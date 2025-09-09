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
<div class="header__button">
    <form action="/logout" method="post">
        @csrf
        <button class="header__button-logout">ログアウト</button>
    </form>
    <form action="" method="get">
        <button class="header__button-mypage">マイページ</button>
    </form>
    <form action="" method="get">
        <button class="header__button-sell">出品</button>
    </form>
</div>
@endsection
@section('content')
<div class="items__menu">
    <a href="/">おすすめ</a>　　　　　<a href="/?tab=mylist" class="item__menu-link">マイリスト</a>
</div>
<hr />
<div class="items__content">
    <div class="items__image">
        <img src="" alt="商品画像" class="items__image-content" /><br />
        商品名
    </div>
    <div class="items__image">
        <img src="" alt="商品画像" class="items__image-content" /><br />
        商品名
    </div>
    <div class="items__image">
        <img src="" alt="商品画像" class="items__image-content" /><br />
        商品名
    </div>
    <div class="items__image">
        <img src="" alt="商品画像" class="items__image-content" /><br />
        商品名
    </div>
    <div class="items__image">
        <img src="" alt="商品画像" class="items__image-content" /><br />
        商品名
    </div>
    <div class="items__image">
        <img src="" alt="商品画像" class="items__image-content" /><br />
        商品名
    </div>
    <div class="items__image">
        <img src="" alt="商品画像" class="items__image-content" /><br />
        商品名
    </div>
    <div class="items__image">
        <img src="" alt="商品画像" class="items__image-content" /><br />
        商品名
    </div>
    <div class="items__image">
        <img src="" alt="商品画像" class="items__image-content" /><br />
        商品名
    </div>
    <div class="items__image">
        <img src="" alt="商品画像" class="items__image-content" /><br />
        商品名
    </div>
</div>
@endsection