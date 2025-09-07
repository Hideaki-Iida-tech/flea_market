@extends('layouts.app')
@section('css')
<link rel="stylesheet" href="{{ asset('css/order/create.css') }}">
@endsection
@section('title')
商品購入画面
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
<div class="purchase__content">
    <div class="purchase__setting">
        <div class="purchase__item">
            <div class="item__image">
                <img src="" alt="商品画像" class="item__img">
            </div>
            <div>
                <div class="item__name">
                    商品名
                </div>
                <div class="price">
                    ¥ 47,000
                </div>
            </div>
        </div>
        <hr />
        <div class="payment-method__title">
            支払い方法
        </div>
        <select class="payment-method__select">
            <option value="">選択してください</option>
            <option value="1">コンビニ払い</option>
            <option value="2">カード払い</option>
        </select>
        <hr />
        <div class="address__header">
            <div class="address__title">
                配送先
            </div>
            <div class="address__edit">
                <a href="" class="address__edit--link">変更する</a>
            </div>
        </div>
        <div class="address__content">
            〒 XXX-YYYY<br />
            ここには住所と建物が入ります
        </div>
        <hr />
    </div>
    <div class="purchase__confirm-submit">
        <table class=price__table>
            <tr>
                <th>
                    商品代金
                </th>
                <td>¥47,000</td>
            </tr>
            <tr>
                <th>
                    支払い方法
                </th>
                <td>
                    コンビニ払い
                </td>
            </tr>
        </table>
        <button class="purchase__button-submit">
            購入する
        </button>
    </div>
</div>
@endsection