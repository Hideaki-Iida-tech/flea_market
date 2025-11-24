@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/auth/cancel.css') }}">
@endsection

@section('title')
支払処理キャンセル
@endsection

@section('content')
<div class="cancel-form">
    <div class="cancel-form-inner">

        <div class="cancel-message">
            支払処理がキャンセルされました。
        </div>

        <div class="cancel-exec">
            <form action="/" method="get">
                <button class="cancel-exec-button">一覧画面にもどる</button>
            </form>
        </div>

    </div>
</div>
@endsection