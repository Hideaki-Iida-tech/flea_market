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
    <form action="" method="post">
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
    <form action="" class="profile__form-inner" method="post">
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
                        <input type="text" class="profile__input" name="name" />
                    </label>
                </td>
            </tr>
            <tr class="profile__form-row">
                <td>
                    <label class="profile__form-label">郵便番号<br /><input type="text" class="profile__input" name="postal_code" />
                    </label>
                </td>
            </tr>
            <tr class="profile__form-row">
                <td>
                    <label class="profile__form-label">住所<br /><input type="text" class="profile__input" name="address" /></label>
                </td>
            </tr>
            <tr class="profile__form-row">
                <td>
                    <label class="profile__form-label">建物名<br /><input type="text" class="profile__input" name="building" /></label>
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