@extends('layouts.app')
@section('css')
<link rel="stylesheet" href="{{ asset('css/address/edit.css') }}">
@endsection
@section('title')
送付先住所変更画面
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
<div class="address-form">
    <form action="/purchase/address/{{ $item->id }}" class="address-form-inner" method="post">
        @csrf
        <table class="address-form-table">
            <tr class="address-form-row-first">
                <td>
                    <h1>住所の変更</h1>
                </td>
            </tr>
            <tr class="address-form-row">
                <td>
                    <label class="address-form-label">
                        郵便番号<br />
                        <input type="text" class="address-input" name="postal_code" value="{{ old('postal_code', $postal_code) }}" />
                    </label>
                    @if ($errors->has('postal_code'))
                    <div class="address-alert-danger">
                        <ul>
                            @foreach ($errors->get('postal_code') as $error)
                            <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                    @endif
                </td>
            </tr>
            <tr class="address-form-row">
                <td>
                    <label class="address-form-label">住所<br /><input type="text" class="address-input" name="address" value="{{ old('address', $address) }}" />
                    </label>
                    @if ($errors->has('address'))
                    <div class="address-alert-danger">
                        <ul>
                            @foreach ($errors->get('address') as $error)
                            <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                    @endif
                </td>
            </tr>
            <tr class="address-form-row">
                <td>
                    <label class="address-form-label">建物名<br /><input type="text" class="address-input" name="building" value="{{ old('building', $building) }}" /></label>
                    @if ($errors->has('building'))
                    <div class="address-alert-danger">
                        <ul>
                            @foreach ($errors->get('building') as $error)
                            <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                    @endif
                </td>
            </tr>
            <tr class="address-form-row">
                <td class="address-form-col-button">
                    <button type="submit" class="address-button">更新する<br /></button>
                </td>
            </tr>
        </table>
    </form>
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