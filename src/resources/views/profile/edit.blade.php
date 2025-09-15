@extends('layouts.app')
@section('css')
<link rel="stylesheet" href="{{ asset('css/profile/edit.css') }}">
@endsection
@section('title')
プロフィール編集画面（設定画面）
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
<div class="profile__form">
    <form action="/mypage/profile" class="profile__form-inner" method="post" enctype="multipart/form-data">
        @csrf
        <table class="profile__form-table">
            <tr class="profile__form-row-first">
                <td>
                    <h1>プロフィール設定</h1>
                </td>
            </tr>
            <tr>
                <td>
                    @php
                    $currentPath = old('current_profile_image',optional(auth()->user())->profile_image);
                    $currentUrl = $currentPath ? Storage::disk('public')->url($currentPath) : 'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAIAAACQd1PeAAAADElEQVR4nGM4c+YMAATMAmU5mmUsAAAAAElFTkSuQmCC'
                    @endphp
                    <img id="profilePreview" class="profile__img" src="{{ $currentUrl }}" alt="プロフィール画像" />
                    <input type="hidden" name="current_profile_image" value="{{ $currentPath }}" />
                    <button id="pickImageBtn" type="button" class="profile__img-button">画像を選択する</button>
                    <input id="profileImageInput" type="file" name="profile_image" accept=".jpeg,.png" style="display:none" />
                    @if ($errors->has('profile_image'))
                    <div class="profile__alert-danger">
                        <ul>
                            @foreach ($errors->get('profile_image') as $error)
                            <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                    @endif
                </td>
            </tr>
            <tr class="profile__form-row">
                <td>
                    <label class="profile__form-label">
                        ユーザー名<br />
                        <input type="text" class="profile__input" name="name" value="{{ old('name',optional(auth()->user())->name) }}" />
                    </label>
                    @if ($errors->has('name'))
                    <div class="profile__alert-danger">
                        <ul>
                            @foreach ($errors->get('name') as $error)
                            <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                    @endif
                </td>
            </tr>
            <tr class="profile__form-row">
                <td>
                    <label class="profile__form-label">郵便番号<br /><input type="text" class="profile__input" name="postal_code" value="{{ old('postal_code',auth()->user()->postal_code) }}" />
                    </label>
                    @if ($errors->has('postal_code'))
                    <div class="profile__alert-danger">
                        <ul>
                            @foreach ($errors->get('postal_code') as $error)
                            <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                    @endif
                </td>
            </tr>
            <tr class="profile__form-row">
                <td>
                    <label class="profile__form-label">住所<br /><input type="text" class="profile__input" name="address" value="{{ old('address',auth()->user()->address) }}" /></label>
                    @if ($errors->has('address'))
                    <div class="profile__alert-danger">
                        <ul>
                            @foreach ($errors->get('address') as $error)
                            <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                    @endif
                </td>
            </tr>
            <tr class="profile__form-row">
                <td>
                    <label class="profile__form-label">建物名<br /><input type="text" class="profile__input" name="building" value="{{ old('building',auth()->user()->building) }}" /></label>
                    @if ($errors->has('building'))
                    <div class="profile__alert-danger">
                        <ul>
                            @foreach ($errors->get('building') as $error)
                            <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                    @endif
                </td>
            </tr>
            <tr class="profile__form-row">
                <td class="profile__form-col-button">
                    <button type="submit" class="profile__button">更新する<br /></button>
                </td>
            </tr>
        </table>
    </form>
</div>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const btn = document.getElementById('pickImageBtn');
        const input = document.getElementById('profileImageInput');
        const img = document.getElementById('profilePreview');
        const placeholder = "data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAIAAACQd1PeAAAADElEQVR4nGM4c+YMAATMAmU5mmUsAAAAAElFTkSuQmCC";

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
                img.src = img.src || placeholder;
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