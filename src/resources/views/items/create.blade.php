@extends('layouts.app')
@section('css')
<link rel="stylesheet" href="{{ asset('css/items/create.css') }}">
@endsection
@section('title')
商品出品画面
@endsection
@section('input')
<form id="search-form" action="/" method="get">
    <input type="text" id="search-box" name="keyword" class="header-search" placeholder="何をお探しですか？" value="{{ old('keyword',$keyword ?? '') }}" />
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
<div class="header-button">
    <form action="/logout" method="post">
        @csrf
        <button class="header-button-logout">ログアウト</button>
    </form>
    <form action="/mypage" method="get">
        <button class="header-button-mypage">マイページ</button>
    </form>
    <form action="/sell" method="get">
        <button class="header-button-sell">出品</button>
    </form>
</div>
@endsection
@section('content')
<div class="sell-content">
    <form action="/sell" method="post" enctype="multipart/form-data">
        @csrf
        <div class="sell-title">
            <h1>商品の出品</h1>
        </div>
        <div class="sell-image-title">
            商品画像
        </div>
        <div class="sell-image-content">
            @php
            $currentPath = old('current_item_image');
            $currentUrl = $currentPath ? Storage::url($currentPath) : 'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAIAAACQd1PeAAAADElEQVR4nGM4c+YMAATMAmU5mmUsAAAAAElFTkSuQmCC';
            @endphp
            <img id="itemPreview" src="{{ $currentUrl }}" alt="" class="sell-img" />
            <input type="hidden" name="current_item_image" value="{{ $currentPath }}" />
            <input id="fileInput" class="file-input" type="file" name="item_image" accept=".jpeg,.png" style="display:none" />
            <label id="pickImageBtn" for="fileInput" class="upload-button">画像を選択する</label>
        </div>
        @error('item_image')
        <div class="sell-alert-danger">
            <ul>
                <li>{{ $message }}</li>
            </ul>
        </div>
        @enderror
        <div class="sell-detail">
            <h2 class="sell-detail-title">商品の詳細</h2>
            <hr />
            <h3>カテゴリー</h3>
            <div class="chip-list">

                @foreach($categories as $category)
                <input type="checkbox" id="cat-{{ $category['id'] }}" class="chip-input" name="categories[]" value="{{ $category['id'] }}" {{ in_array($category['id'], old('categories',[])) ? 'checked' : '' }} />
                <label for="cat-{{ $category['id'] }}" class="chip">{{ $category['name'] }}</label>
                @endforeach

            </div>
            @error('categories')
            <div class="sell-alert-danger">
                <ul>
                    <li>{{ $message }}</li>
                </ul>
            </div>
            @enderror
            @foreach($errors->get('categories.*') as $error)
            <div class="sell-alert-danger">
                <ul>
                    @foreach ($error as $message)
                    <li>{{ $message }}</li>
                    @endforeach
                </ul>
            </div>
            @endforeach
            <h3>商品の状態</h3>
            <select name="condition_id" class="sell-detail-condition">
                <option value="0">選択してください</option>
                @foreach($conditions as $condition)
                <option value="{{ $condition['id'] }}" {{ old('condition_id') == $condition['id'] ? 'selected' : ''}}>{{ $condition['name'] }}</option>
                @endforeach
            </select>
            @if ($errors->has('condition_id'))
            <div class="sell-alert-danger">
                <ul>
                    @foreach ($errors->get('condition_id') as $error)
                    <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
            @endif
        </div>
        <div class="name-and-description">
            <h2 class="name-and-description-title">商品名と説明</h2>
            <hr />
            <h3>商品名</h3>
            <input type="text" name="item_name" class="name-and-description-input" value="{{ old('item_name') }}" />
            @if ($errors->has('item_name'))
            <div class="sell-alert-danger">
                <ul>
                    @foreach ($errors->get('item_name') as $error)
                    <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
            @endif
            <h3>ブランド名</h3>
            <input type="text" name="brand" class="name-and-description-input" value="{{ old('brand') }}" />
            @if ($errors->has('brand'))
            <div class="sell-alert-danger">
                <ul>
                    @foreach ($errors->get('brand') as $error)
                    <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
            @endif
            <h3>商品の説明</h3>
            <textarea name="description" class="description">{{ old('description') }}</textarea>
            @if ($errors->has('description'))
            <div class="sell-alert-danger">
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
                <input type="text" name="price" class="name-and-description-input" value="{{ old('price') }}" />
            </div>
            @if ($errors->has('price'))
            <div class="sell-alert-danger">
                <ul>
                    @foreach ($errors->get('price') as $error)
                    <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
            @endif
        </div>
        <div class="sell-submit">
            <button class="sell-submit-button">出品する</button>
        </div>
    </form>
</div>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const btn = document.getElementById('pickImageBtn');
        const input = document.getElementById('fileInput');
        const img = document.getElementById('itemPreview');

        btn.addEventListener('click', function(e) {
            e.preventDefault();
            input.click();
        });

        input.addEventListener('change', function() {
            const file = this.files && this.files[0];
            if (!file) return;

            // 拡張子＆MIMEの双方でチェック（どちらか片方だけだと穴があるため）
            const allowExts = new Set(['jpeg', 'png']);
            const allowMimes = new Set(['image/jpeg', 'image/png']);

            const ext = (file.name.split('.').pop() || '').toLowerCase();
            const ok = allowExts.has(ext) && allowMimes.has(file.type);

            if (!ok) {
                alert('JPEG(.jpeg)またはPNG(.png)のみ選択できます');
                this.value = '';
                //img.src = img.src;
                return;
            }

            const url = URL.createObjectURL(file);
            img.src = url;
            img.onload = () => URL.revokeObjectURL(url);
            img.onerror = () => URL.revokeObjectURL(url);
        });
    });
</script>
@endsection