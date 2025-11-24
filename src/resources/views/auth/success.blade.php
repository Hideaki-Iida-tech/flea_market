@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/auth/success.css') }}">
@endsection

@section('title')
支払処理成功
@endsection

@section('content')
<div class="success-form">
    <div class="success-form-inner">

        <div class="success-message">
            支払処理が成功しました。
        </div>

        <div class="success-exec">
            <form action="/" method="get">
                <button class="success-exec-button">一覧画面にもどる</button>
            </form>
        </div>

    </div>
</div>
@endsection