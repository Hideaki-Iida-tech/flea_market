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
                <div class="price-info">
                    ¥{{ number_format($item['price']) }}
                </div>
            </div>
        </div>
        <hr />
        <div class="payment-method__title">
            支払い方法
        </div>
        <select id="paymentSelect" class="payment-method__select" form="order-form" name="payment_method">
            <option value="">選択してください</option>
            @foreach($paymentLabels as $key => $value)
            <option value="{{ $key }}">{{ $value }}</option>
            @endforeach
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
            <textarea name="address" id="" class="address__textarea" readonly>〒 {{ $user->postal_code }}
{{ $user->address }} {{ $user->building }}</textarea>

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
                <th class="price-table-col">
                    商品代金
                </th>
                <td>¥{{ number_format($item['price']) }}</td>
            </tr>
            <tr>
                <th class="price-table-col">
                    支払い方法
                </th>
                <td>
                    <span id="selectedValue">
                        選択してください
                    </span>
                </td>
            </tr>
        </table>
        @if(!$sold)
        <form id="order-form" action="/purchase/{{$item['id']}}" method="post">
            @csrf
            {{--<input type="hidden" name="user_id" value="{{ auth()->id() }}" />--}}
            {{--<input type="hidden" name="item_id" value="{{ $item['id'] }}" />--}}
            <input type="hidden" name="price" value="{{ $item['price'] }}" />
            <input type="hidden" name="postal_code" value="{{ $user->postal_code }}" />
            <input type="hidden" name="address" value="{{ $user->address }}" />
            <input type="hidden" name="building" value="{{ $user->building }}" />
            <button class="purchase__button-submit">
                購入する
            </button>
        </form>
        @endif
    </div>
</div>
<script>
    const select = document.getElementById('paymentSelect');
    const valueOutput = document.getElementById('selectedValue');
    select.addEventListener('change', function() {
        const selectedOption = this.options[this.selectedIndex];
        valueOutput.textContent = selectedOption.text;
    });
</script>
@endsection