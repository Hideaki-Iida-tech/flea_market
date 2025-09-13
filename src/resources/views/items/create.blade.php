@extends('layouts.app')
@section('css')
<link rel="stylesheet" href="{{ asset('css/items/create.css') }}">
@endsection
@section('title')
商品出品画面
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
<div class="sell__content">
    <form action="">
        <div class="sell__title">
            <h1>商品の出品</h1>
        </div>
        <div class="sell__image--title">
            商品画像
        </div>
        <div class="sell__image--content">
            <img src="" alt="" class="sell__img" />
            <input type="file" id="file-input" class="file-input" />
            <label for="file-input" class="upload-button">画像を選択する</label>
        </div>
        <div class="sell__detail">
            <h2 class="sell__detail--title">商品の詳細</h2>
            <hr />
            <h3>カテゴリー</h3>
            <div class="chip-list">
                <!--ループで量産-->
                <input type="checkbox" id="cat-1" class="chip-input" name="categories[]" />
                <label for="cat-1" class="chip">ファッション</label>
                <input type="checkbox" id="cat-2" class="chip-input" name="categories[]" />
                <label for="cat-2" class="chip">家電</label>
                <input type="checkbox" id="cat-3" class="chip-input" name="categories[]" />
                <label for="cat-3" class="chip">インテリア</label>
                <input type="checkbox" id="cat-4" class="chip-input" name="categories[]" />
                <label for="cat-4" class="chip">レディース</label>
                <input type="checkbox" id="cat-5" class="chip-input">
                <label for="cat-5" class="chip">メンズ</label>
                <input type="checkbox" id="cat-6" class="chip-input" name="categories[]" />
                <label for="cat-6" class="chip">コスメ</label>
                <input type="checkbox" id="cat-7" class="chip-input" name="categories[]" />
                <label for="cat-7" class="chip">本</label>
                <input type="checkbox" id="cat-8" class="chip-input">
                <label for="cat-8" class="chip">ゲーム</label>
                <input type="checkbox" id="cat-9" class="chip-input" name="categories[]" />
                <label for="cat-9" class="chip">スポーツ</label>
                <input type="checkbox" id="cat-10" class="chip-input" name="categories[]" />
                <label for="cat-10" class="chip">キッチン</label>
                <input type="checkbox" id="cat-11" class="chip-input" name="categories[]" />
                <label for="cat-11" class="chip">ハンドメイド</label>
                <input type="checkbox" id="cat-12" class="chip-input" name="categories[]" />
                <label for="cat-12" class="chip">アクセサリー</label>
                <input type="checkbox" id="cat-13" class="chip-input" name="categories[]" />
                <label for="cat-13" class="chip">おもちゃ</label>
                <input type="checkbox" id="cat-14" class="chip-input" name="categories[]" />
                <label for="cat-14" class="chip">ベビー・キッズ</label>
                <!-- -->
            </div>
            @error('categories')
            <div class="sell__alert-danger">{{ $message }}</div>
            @enderror
            @foreach($errors->get('categories.*') as $error)
            <div class="cell__alert-danger">
                <ul>
                    @foreach ($error as $message)
                    <li>{{ $message }}</li>
                    @endforeach
                </ul>
            </div>
            @endforeach
            <h3>商品の状態</h3>
            <select name="condition_id" class="sell__detail--condition">
                <option value="0">選択してください</option>
                <option value="1">目立った傷や汚れなし</option>
                <option value="2">やや傷や汚れあり</option>
                <option value="3">状態が悪い</option>
            </select>
            @if ($errors->has('condition_id'))
            <div class="sell__alert-danger">
                <ul>
                    @foreach ($errors->get('condition_id') as $error)
                    <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
            @endif
        </div>
        <div class="name-and-description">
            <h2 class="name-and-description__title">商品名と説明</h2>
            <hr />
            <h3>商品名</h3>
            <input type="text" name="item_name" class="name-and-description__input" />
            @if ($errors->has('item_name'))
            <div class="sell__alert-danger">
                <ul>
                    @foreach ($errors->get('item_name') as $error)
                    <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
            @endif
            <h3>ブランド名</h3>
            <input type="text" name="brand" class="name-and-description__input" />
            @if ($errors->has('brand'))
            <div class="sell__alert-danger">
                <ul>
                    @foreach ($errors->get('brand') as $error)
                    <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
            @endif
            <h3>商品の説明</h3>
            <textarea name="description" class="description"></textarea>
            @if ($errors->has('description'))
            <div class="sell__alert-danger">
                <ul>
                    @foreach ($errors->get('description') as $error)
                    <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
            @endif
            <h3>販売価格</h3>
            <div class="input-wrapper">
                <span class="prefix">￥</span>
                <input type="text" name="price" class="name-and-description__input" />
            </div>
            @if ($errors->has('price'))
            <div class="sell__alert-danger">
                <ul>
                    @foreach ($errors->get('price') as $error)
                    <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
            @endif
        </div>
        <div class="sell__submit">
            <button class="sell__submit-button">出品する</button>
        </div>
    </form>
</div>
@endsection