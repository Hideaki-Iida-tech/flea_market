@extends('layouts.app')
@section('css')
<link rel="stylesheet" href="{{ asset('css/auth/verify-email.css') }}">
@endsection
@section('title')
メール認証誘導画面
@endsection
@section('content')
<div class="verify-email__form">
    <form action="" class="verify-email__form-inner" method="post">
        <div class="verify-email__message">
            登録いただいたメールアドレスに認証メールを送付しました。<br />
            メール認証を完了してください。
        </div>
        <div class="verify-email__exec">
            <button class="verify-email__exec-button">認証はこちらから</button>
        </div>
        <div class="verify-email__resend">
            <button class="verify-email__resend-button">認証メールを再送する</button>
        </div>
    </form>
</div>
@endsection