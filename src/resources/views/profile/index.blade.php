@extends('layouts.app')
@section('css')
<link rel="stylesheet" href="{{ asset('css/profile/index.css') }}">
@endsection
@section('title')
プロフィール画面
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
    <form action="/mypage" method="get">
        <button class="header__button-mypage">マイページ</button>
    </form>
    <form action="/sell" method="get">
        <button class="header__button-sell">出品</button>
    </form>
</div>
@endsection
@section('content')
<div class="user-info">
    <div class="user-info-inner">
        @php
        $currentImage = optional(auth()->user())->profile_image ? asset('storage/' . optional(auth()->user())->profile_image) : '';
        @endphp
        <img class="profile__img" src="{{ $currentImage }}" alt="" />
        <h1>{{ optional(auth()->user())->name }}</h1>
    </div>
    <form action="/mypage/profile" method="get">
        <button class="profile__img-button">　プロフィールを編集　　</button>
    </form>
</div>
<div class="profile__menu">
    <a href="/mypage?page=sell" class="profile__menu-link">出品した商品</a>　　　　　<a href="/mypage?page=buy">購入した商品</a>
</div>
<hr />
<div class="profile__content">
    <div class="profile__image">
        <img src="" alt="商品画像" class="profile__image-content" /><br />
        商品名
    </div>
    <div class="profile__image">
        <img src="" alt="商品画像" class="profile__image-content" /><br />
        商品名
    </div>
    <div class="profile__image">
        <img src="" alt="商品画像" class="profile__image-content" /><br />
        商品名
    </div>
    <div class="profile__image">
        <img src="" alt="商品画像" class="profile__image-content" /><br />
        商品名
    </div>
    <div class="profile__image">
        <img src="" alt="商品画像" class="profile__image-content" /><br />
        商品名
    </div>
    <div class="profile__image">
        <img src="" alt="商品画像" class="profile__image-content" /><br />
        商品名
    </div>
    <div class="profile__image">
        <img src="" alt="商品画像" class="profile__image-content" /><br />
        商品名
    </div>
    <div class="profile__image">
        <img src="" alt="商品画像" class="profile__image-content" /><br />
        商品名
    </div>
    <div class="profile__image">
        <img src="" alt="商品画像" class="profile__image-content" /><br />
        商品名
    </div>
    <div class="profile__image">
        <img src="" alt="商品画像" class="profile__image-content" /><br />
        商品名
    </div>
</div>
@endsection