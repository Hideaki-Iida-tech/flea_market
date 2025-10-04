@extends('layouts.app')
@section('css')
<link rel="stylesheet" href="{{ asset('css/order/create.css') }}">
@endsection
@section('title')
商品購入画面
@endsection
@section('input')
<form id="search-form" action="/" method="get">
    <input type="text" id="search-box" name="keyword" class="header__search" placeholder="何をお探しですか？" value="{{ old('keyword',$keyword ?? '') }}" />
</form>
<script>
    document.getElementById('search-box').addEventListener('keydown', function(e) {
        if (e.key === 'Enter') {
            e.preventDefault();
            document.getElementById('search-form').submit();
        }
    });
</script>
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
            <div class="item__image">
                <img src="{{ $item->image_url }}" alt="商品画像" class="item__img">
            </div>
            <div>
                <div class="item__name">
                    {{ $item->item_name }}
                </div>
                <div class="price-info">
                    ¥{{ number_format($item->price) }}
                </div>
            </div>
        </div>
        <hr />
        <div class="payment-method__title">
            支払い方法
        </div>
        @php
        // null のときは '' に倒す（未選択扱いを安定化）
        $selectedPayment = old('payment_method',session("order_draft.{$item->id}.payment_method")) ?? '';
        @endphp
        <select id="paymentSelect" class="payment-method__select" form="order-form" name="payment_method">
            <option value="" {{ $selectedPayment=='' ? 'selected' : '' }}>選択してください</option>
            @foreach($paymentLabels as $key => $value)
            <option value="{{ $key }}" {{ $selectedPayment==$key ? 'selected' : ''}}>{{ $value }}</option>
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
                <a href="/purchase/address/{{ $item->id }}" class="address__edit--link">変更する</a>
            </div>
        </div>
        <div class="address__content">
            <textarea name="address" id="" class="address__textarea" readonly>〒 {{ $postal_code }}
{{ $address }} {{ $building }}</textarea>

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
            <tr class="price-table-row">
                <th class="price-table-col">
                    商品代金
                </th>
                <td>¥{{ number_format($item->price) }}</td>
            </tr>
            <tr class="price-table-row">
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
        <form id="order-form" action="/purchase/{{$item->id}}" method="post">
            @csrf
            {{--<input type="hidden" name="user_id" value="{{ auth()->id() }}" />--}}
            {{--<input type="hidden" name="item_id" value="{{ $item->id }}" />--}}
            <input type="hidden" name="price" value="{{ $item->price }}" />
            <input type="hidden" name="postal_code" value="{{ $postal_code }}" />
            <input type="hidden" name="address" value="{{ $address }}" />
            <input type="hidden" name="building" value="{{ $building }}" />
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
    const token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

    // 初期表示で同期
    const initOpt = select.options[select.selectedIndex];
    if (initOpt) valueOutput.textContent = initOpt.text;

    select.addEventListener('change', function() {
        const selectedOption = this.options[this.selectedIndex];
        valueOutput.textContent = selectedOption.text;
    });

    select.addEventListener('change', async function() {
        await fetch("/purchase/{{$item->id}}/payment/draft", {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': token,
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({
                payment_method: this.value
            })
        });
    });
</script>
@endsection