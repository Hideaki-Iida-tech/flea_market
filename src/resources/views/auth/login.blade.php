@extends('layouts.app')
@section('css')
<link rel="stylesheet" href="{{ asset('css/auth/login.css') }}">
@endsection
@section('title')
ログイン画面
@endsection
@section('content')
<div class="login__form">
    <form action="/login" class="login__form-inner" method="post">
        @csrf
        <table class="login__form-table">
            <tr class="login__form-row-first">
                <td>
                    <h1>ログイン</h1>
                </td>
            </tr>
            <tr class="login__form-row">
                <td>
                    <label class="login__form-label">メールアドレス<br /><input type="email" class="login__input" name="email" value="{{ old('email') }}" />
                    </label>
                </td>
            </tr>
            <tr class="login__form-row">
                <td>
                    <label class="login__form-label">パスワード<br /><input type="password" class="login__input" name="password" /></label>
                </td>
            </tr>
            <tr class="login__form-row">
                <td class="login__form-col-button">
                    <button type="submit" class="login__button">ログインする<br /></button>
                </td>
            </tr>
            <tr class="login__form-row-last">
                <td class="login__form-col-link">
                    <a href="/register" class="login__form-link">会員登録はこちら</a>
                </td>
            </tr>
        </table>
    </form>
</div>
@endsection