@extends('layouts.app')
@section('css')
<link rel="stylesheet" href="{{ asset('css/items/index.css') }}">
@endsection
@section('title')
商品一覧画面
@endsection
@section('input')
<form id="search-form" action="/" method="get">
    <input type="text" id="search-box" name="keyword" class="header-search" placeholder="何をお探しですか？" value="{{ old('keyword',$keyword ?? '') }}" />
</form>
@endsection
@section('button')
@if(Auth::check())
<div class=" header-button">
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

<div class="items-menu">
    <a href="{{ $query ? $base . '?' . http_build_query($query) : $base }}" class="items-menu-reconment">おすすめ</a>
    <a href="{{ $base . '?' . http_build_query(array_merge(['tab' => 'mylist'], $query)) }}" class="items-menu-link">マイリスト</a>
</div>
<hr />
<div class="items-content">

    @foreach($items as $item)
    @if((auth()->id() !== $item->user_id) || $mylist)

    <div class="items-image">
        <a href="/item/{{ $item->id }}">
            <img src="{{ $item->image_url }}" alt="商品画像" class="items-image-content" />
            <div>
                {{ $item->item_name }}
                @if($item->order)
                <span class="sold">Sold</span>
                @endif
            </div>
        </a>
    </div>
    @endif
    @endforeach
</div>
<script>
    document.getElementById('search-box').addEventListener('keydown', function(e) {
        if (e.key === 'Enter') {
            e.preventDefault();
            document.getElementById('search-form').submit();
        }
    });
</script>
@endsection