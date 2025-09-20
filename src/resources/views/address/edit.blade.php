@extends('layouts.app')
@section('css')
<link rel="stylesheet" href="{{ asset('css/address/edit.css') }}">
@endsection
@section('title')
送付先住所変更画面
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
<div class="address__form">
    <form action="" class="address__form-inner" method="post">
        <table class="address__form-table">
            <tr class="address__form-row-first">
                <td>
                    <h1>住所の変更</h1>
                </td>
            </tr>
            <tr class="address__form-row">
                <td>
                    <label class="address__form-label">
                        郵便番号<br />
                        <input type="text" class="address__input" name="postal_code" />
                    </label>
                    @if ($errors->has('postal_code'))
                    <div class="address__alert-danger">
                        <ul>
                            @foreach ($errors->get('postal_code') as $error)
                            <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                    @endif
                </td>
            </tr>
            <tr class="address__form-row">
                <td>
                    <label class="address__form-label">住所<br /><input type="text" class="address__input" name="address" />
                    </label>
                    @if ($errors->has('address'))
                    <div class="address__alert-danger">
                        <ul>
                            @foreach ($errors->get('address') as $error)
                            <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                    @endif
                </td>
            </tr>
            <tr class="address__form-row">
                <td>
                    <label class="address__form-label">建物名<br /><input type="text" class="address__input" name="building" /></label>
                    @if ($errors->has('building'))
                    <div class="address__alert-danger">
                        <ul>
                            @foreach ($errors->get('building') as $error)
                            <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                    @endif
                </td>
            </tr>
            <tr class="address__form-row">
                <td class="address__form-col-button">
                    <button type="submit" class="address__button">更新する<br /></button>
                </td>
            </tr>
        </table>
    </form>
</div>
@endsection