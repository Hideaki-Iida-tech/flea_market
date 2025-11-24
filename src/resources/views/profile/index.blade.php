@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/profile/index.css') }}">
@endsection

@section('title')
プロフィール画面
@endsection

@section('input')
<form id="search-form" action="/" method="get">
    <input type="text" id="search-box" name="keyword" class="header-search" placeholder="何をお探しですか？" value="{{ old('keyword',$keyword ?? '') }}" />
</form>
@endsection

@section('button')
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
@endsection

@section('content')
<div class="user-info">

    <div class="user-info-inner">
        <img class="profile-img" src="{{ $currentImage }}" alt="" />
        <h1>{{ optional(auth()->user())->name }}</h1>
    </div>

    <form action="/mypage/profile" method="get">
        <button class="profile-img-button">プロフィールを編集</button>
    </form>

</div>

<div class="profile-menu">
    <a href="/mypage?page=sell" class="profile-menu-link">出品した商品</a>
    <a href="/mypage?page=buy" class="profile-menu-buy">購入した商品</a>
</div>
<hr />

<div class="profile-content">
    @if(!empty($items))
    @foreach($items as $item)
    <div class="profile-image">
        <a href="/item/{{ $item->id }}">
            <img src="{{ $item->image_url }}" alt="商品画像" class="profile-image-content" /><br />
            {{ $item->item_name }}
            @if($item->order)
            <span class="sold">Sold</span>
            @endif
        </a>
    </div>
    @endforeach
    @endif
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
</script>
@endsection