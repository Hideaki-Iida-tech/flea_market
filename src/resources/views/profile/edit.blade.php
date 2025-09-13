@extends('layouts.app')
@section('css')
<link rel="stylesheet" href="{{ asset('css/profile/edit.css') }}">
@endsection
@section('title')
プロフィール編集画面（設定画面）
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
<div class="profile__form">
    <form action="/mypage/profile" class="profile__form-inner" method="post">
        @csrf
        <table class="profile__form-table">
            <tr class="profile__form-row-first">
                <td>
                    <h1>プロフィール設定</h1>
                </td>
            </tr>
            <tr>
                <td>
                    <img class="profile__img" src="" alt="" />
                    <button class="profile__img-button">画像を選択する</button>
                </td>
            </tr>
            <tr class="profile__form-row">
                <td>
                    <label class="profile__form-label">
                        ユーザー名<br />
                        <input type="text" class="profile__input" name="name" value="{{ old('name',optional(auth()->user())->name) }}" />
                    </label>
                    @if ($errors->has('name'))
                    <div class="profile__alert-danger">
                        <ul>
                            @foreach ($errors->get('name') as $error)
                            <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                    @endif
                </td>
            </tr>
            <tr class="profile__form-row">
                <td>
                    <label class="profile__form-label">郵便番号<br /><input type="text" class="profile__input" name="postal_code" value="{{ old('postal_code',auth()->user()->postal_code) }}" />
                    </label>
                    @if ($errors->has('postal_code'))
                    <div class="profile__alert-danger">
                        <ul>
                            @foreach ($errors->get('postal_code') as $error)
                            <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                    @endif
                </td>
            </tr>
            <tr class="profile__form-row">
                <td>
                    <label class="profile__form-label">住所<br /><input type="text" class="profile__input" name="address" value="{{ old('address',auth()->user()->address) }}" /></label>
                    @if ($errors->has('address'))
                    <div class="profile__alert-danger">
                        <ul>
                            @foreach ($errors->get('address') as $error)
                            <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                    @endif
                </td>
            </tr>
            <tr class="profile__form-row">
                <td>
                    <label class="profile__form-label">建物名<br /><input type="text" class="profile__input" name="building" value="{{ old('building',auth()->user()->building) }}" /></label>
                    @if ($errors->has('building'))
                    <div class="profile__alert-danger">
                        <ul>
                            @foreach ($errors->get('building') as $error)
                            <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                    @endif
                </td>
            </tr>
            <tr class="profile__form-row">
                <td class="profile__form-col-button">
                    <button type="submit" class="profile__button">更新する<br /></button>
                </td>
            </tr>
        </table>
    </form>
</div>
@endsection