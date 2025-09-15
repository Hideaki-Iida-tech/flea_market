@extends('layouts.app')
@section('css')
<link rel="stylesheet" href="{{ asset('css/items/show.css') }}">
@endsection
@section('title')
商品詳細画面
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
    <form action="" method="get">
        <button class="header__button-sell">出品</button>
    </form>
</div>
@endsection
@section('content')
<div class="items__content">
    <div class="items__image">
        <img src="" alt="商品画像" class="items__img" />
    </div>
    <div class="items__detail">
        <h1 class="item__name">
            商品名がここに入る
        </h1>
        <div class="brand">
            ブランド名
        </div>
        <div class="price">
            ¥<span class="price__font">47,000</span>（税込）
        </div>
        <div class="icons">
            <div class="like">
                <img src="" alt="いいね" class="like__img">
                <div class="like__count">
                    3
                </div>
            </div>
            <div class="comment">
                <img src="" alt="コメント" class="comment__img">
                <div class="comment__count">
                    1
                </div>
            </div>
        </div>
        <form action="" class="form__purchase">
            <button class="form__purchase-submit">購入手続きへ</button>
        </form>
        <h2>商品説明</h2>
        <div class="description">
            カラー:グレー<br />
            <br />
            新品<br />
            商品の状態は良好です。傷もありません。
            <br />
            購入後、即発送いたします。
        </div>
        <h2>商品の情報</h2>
        <table>
            <tr>
                <th>
                    カテゴリー
                </th>
                <td class="categories__box">
                    <div class="category__name">
                        洋服
                    </div>
                    <div class="category__name">
                        メンズ
                    </div>
                </td>
            </tr>
            <tr>
                <th>
                    商品の状態
                </th>
                <td class="condition__box">
                    <div class="condition__name">
                        良好
                    </div>
                </td>
            </tr>
        </table>
        <h2>コメント(<span class="comment__count">1</span>)</h2>
        <div class="user__info">
            <img src="" alt="プロフィールイメージ" class="profile__image">
            <div class="user__name">admin</div>
        </div>
        <div class="comment__content">ここにコメントが入ります。
        </div>
        <form action="" class="comment__edit">
            <h3>商品へのコメント</h3>
            <textarea name="body" id="" class="comment__body">

            </textarea>
            @if ($errors->has('body'))
            <div class="comment__alert-danger">
                <ul>
                    @foreach ($errors->get('body') as $error)
                    <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
            @endif

            <button class="comment__edit-submit">
                コメントを送信する
            </button>
        </form>
    </div>
</div>
@endsection