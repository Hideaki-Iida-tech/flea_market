@extends('layouts.app')
@section('css')
<link rel="stylesheet" href="{{ asset('css/auth/register.css') }}">
@endsection
@section('title')
会員登録画面
@endsection
@section('content')
<div class="register__form">
    <form action="/register" class="register__form-inner" method="post">
        <table class="register__form-table">
            <tr class="register__form-row-first">
                <td>
                    <h1>会員登録</h1>
                </td>
            </tr>
            <tr class="register__form-row">
                <td>
                    <label class="register__form-label">
                        ユーザー名<br />
                        <input type="text" class="register__input" name="name" />
                    </label>
                </td>
            </tr>
            <tr class="register__form-row">
                <td>
                    <label class="register__form-label">メールアドレス<br /><input type="email" class="register__input" name="email" />
                    </label>
                </td>
            </tr>
            <tr class="register__form-row">
                <td>
                    <label class="register__form-label">パスワード<br /><input type="password" class="register__input" name="password" /></label>
                </td>
            </tr>
            <tr class="register__form-row">
                <td>
                    <label class="register__form-label">確認用パスワード<br /><input type="password" class="register__input" name="password_confirmation" /></label>
                </td>
            </tr>
            <tr class="register__form-row">
                <td class="register__form-col-button">
                    <button type="submit" class="register__button">登録する<br /></button>
                </td>
            </tr>
            <tr class="register__form-row-last">
                <td class="register__form-col-link">
                    <a href="/login" class="register__form-link">ログインはこちら</a>
                </td>
            </tr>
        </table>
    </form>
</div>
@endsection