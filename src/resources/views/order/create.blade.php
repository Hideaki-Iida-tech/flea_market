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
<div class="purchase__content">
    <div class="purchase__setting">
        <div class="purchase__item">
            @php
            if(str_starts_with($item['item_image'],'https://')){
            $currentImage = $item['item_image'];
            }
            elseif($item['item_image']){
            $currentImage = asset('storage/' . $item['item_image']);
            }else{
            $currentImage = '';
            }
            @endphp
            <div class="item__image">
                <img src="{{ $currentImage }}" alt="商品画像" class="item__img">
            </div>
            <div>
                <div class="item__name">
                    {{ $item['item_name'] }}
                </div>
                <div class="price">
                    ¥{{ number_format($item['price']) }}
                </div>
            </div>
        </div>
        <hr />
        <div class="payment-method__title">
            支払い方法
        </div>
        <select class="payment-method__select" name="payment_method">
            <option value="">選択してください</option>
            <option value="1">コンビニ払い</option>
            <option value="2">カード払い</option>
        </select>
        @if ($errors->has('payment_method'))
        <div class="order__alert-danger">
            <ul>
                @foreach ($errors->get('payment_method') as $error)
                <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
        @endif
        <hr />
        <div class="address__header">
            <div class="address__title">
                配送先
            </div>
            <div class="address__edit">
                <a href="/purchase/address/{{ $item['id'] }}}" class="address__edit--link">変更する</a>
            </div>
        </div>
        <div class="address__content">
            <textarea name="address" id="" class="address__textarea" readonly>〒 XXX-YYYY
            ここには住所と建物が入ります</textarea>

        </div>
        @if ($errors->has('address'))
        <div class="order__alert-danger">
            <ul>
                @foreach ($errors->get('address') as $error)
                <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
        @endif
        <hr />
    </div>
    <div class="purchase__confirm-submit">
        <table class=price__table>
            <tr>
                <th>
                    商品代金
                </th>
                <td>¥{{ number_format($item['price']) }}</td>
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