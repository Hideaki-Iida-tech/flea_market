@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/items/error.css') }}">
@endsection

@section('title')
出品処理失敗
@endsection

@section('content')
<div class="error-form">
    <div class="error-form-inner">

        <div class="error-message">
            出品処理が失敗しました。<br />
            管理者に連絡してください。
        </div>

        <div class="error-exec">
            <form action="/" method="get">
                <button class="error-exec-button">一覧画面にもどる</button>
            </form>
        </div>

    </div>
</div>
@endsection