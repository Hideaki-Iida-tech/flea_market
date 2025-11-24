@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/profile/edit.css') }}">
@endsection

@section('title')
プロフィール編集画面（設定画面）
@endsection

@section('input')
<form id="search-form" action="/" method="get">
    <input type="text" id="search-box" name="keyword" class="header-search" placeholder="何をお探しですか？" value="{{ old('keyword',$keyword ?? '') }}" />
</form>
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
<div class="profile-form">
    <form action="/mypage/profile" class="profile-form-inner" method="post" enctype="multipart/form-data">
        @csrf
        <table class="profile-form-table">

            <tr class="profile-form-row-first">
                <td>
                    <h1>プロフィール設定</h1>
                </td>
            </tr>

            <tr>
                <td>
                    <img id="profilePreview" class="profile-img" src="{{ $currentUrl }}" alt="プロフィール画像" />
                    <input type="hidden" name="current_profile_image" value="{{ $currentPath }}" />
                    <button id="pickImageBtn" type="button" class="profile-img-button">画像を選択する</button>
                    <input id="profileImageInput" type="file" name="profile_image" accept=".jpeg,.png" style="display:none" />
                    @if ($errors->has('profile_image'))
                    <div class="profile-alert-danger">
                        <ul>
                            @foreach ($errors->get('profile_image') as $error)
                            <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                    @endif
                </td>
            </tr>

            <tr class="profile-form-row">

                <td>
                    <label class="profile-form-label">
                        ユーザー名<br />
                        <input type="text" class="profile-input" name="name" value="{{ old('name',optional(auth()->user())->name) }}" />
                    </label>
                    @if ($errors->has('name'))
                    <div class="profile-alert-danger">
                        <ul>
                            @foreach ($errors->get('name') as $error)
                            <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                    @endif
                </td>
            </tr>

            <tr class="profile-form-row">
                <td>
                    <label class="profile-form-label">郵便番号<br /><input type="text" class="profile-input" name="postal_code" value="{{ old('postal_code',auth()->user()->postal_code) }}" />
                    </label>
                    @if ($errors->has('postal_code'))
                    <div class="profile-alert-danger">
                        <ul>
                            @foreach ($errors->get('postal_code') as $error)
                            <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                    @endif
                </td>
            </tr>

            <tr class="profile-form-row">
                <td>
                    <label class="profile-form-label">住所<br /><input type="text" class="profile-input" name="address" value="{{ old('address',auth()->user()->address) }}" /></label>
                    @if ($errors->has('address'))
                    <div class="profile-alert-danger">
                        <ul>
                            @foreach ($errors->get('address') as $error)
                            <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                    @endif
                </td>
            </tr>

            <tr class="profile-form-row">
                <td>
                    <label class="profile-form-label">建物名<br /><input type="text" class="profile-input" name="building" value="{{ old('building',auth()->user()->building) }}" /></label>
                    @if ($errors->has('building'))
                    <div class="profile-alert-danger">
                        <ul>
                            @foreach ($errors->get('building') as $error)
                            <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                    @endif
                </td>
            </tr>

            <tr class="profile-form-row">
                <td class="profile-form-col-button">
                    <button type="submit" class="profile-button">更新する<br /></button>
                </td>
            </tr>

        </table>
    </form>
</div>

<script>
    // Enterキー押下でフォームが自動送信されてしまうのを防ぎ、
    // 手動で search-form を送信するためのイベントリスナー
    // 検索ボックスに「商品名」が入力されてEnterキーが押下された場合の挙動
    document.getElementById('search-box').addEventListener('keydown', function(e) {
        // Enterキーが押された場合
        if (e.key === 'Enter') {
            e.preventDefault(); // デフォルトのフォーム送信をキャンセル
            document.getElementById('search-form').submit(); // 明示的にフォーム送信
        }
    });

    // DOMがすべて読み込まれてから実行する（要素の取得ミス防止）
    document.addEventListener('DOMContentLoaded', function() {
        // ボタン・ファイル入力・プレビュー画像・初期画像（プレースホルダー）を取得
        const btn = document.getElementById('pickImageBtn');
        const input = document.getElementById('profileImageInput');
        const img = document.getElementById('profilePreview');
        // base64 の透明1px画像（プレビュー初期化・エラー時に使用）
        const placeholder = "data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAIAAACQd1PeAAAADElEQVR4nGM4c+YMAATMAmU5mmUsAAAAAElFTkSuQmCC";

        // ---- 「画像を選択」ボタンを押すと、実際の input[type=file] をクリックさせる ----
        btn.addEventListener('click', function(e) {
            e.preventDefault(); // ボタンがフォーム内にある場合の意図しない送信を防ぐ
            input.click(); // 隠されたファイル選択ダイアログを開く
        });

        // ---- ファイルが選択されたときにプレビュー処理を実行 ----
        input.addEventListener('change', function() {
            const file = this.files && this.files[0]; // 選択された最初のファイル
            if (!file) return; // 選択キャンセル時などは何もせず終了

            // 許可する拡張子と MIME タイプを定義
            // 両方チェックすることで拡張子偽装・MIME偽装の抜け穴を防ぐ
            const allowExts = new Set(['jpeg', 'png']);
            const allowMimes = new Set(['image/jpeg', 'image/png']);

            // 選択されたファイル名から拡張子を取得（小文字化して比較）
            const ext = (file.name.split('.').pop() || '').toLowerCase();
            // 拡張子・MIME の両方が一致している場合のみ OK とする
            const ok = allowExts.has(ext) && allowMimes.has(file.type);

            // JPEG/PNG 以外のファイルが選択された場合の処理
            if (!ok) {
                alert('JPEG(.jpeg)またはPNG(.png)のみ選択できます');
                // 不正ファイルを選択状態から解除
                this.value = '';
                // プレビュー画像を元のまま表示（なければ placeholder を表示）
                img.src = img.src || placeholder;
                return;
            }

            // ---- プレビュー画像を表示（ObjectURL を使用） ----
            const url = URL.createObjectURL(file);
            img.src = url;

            // メモリリークを防ぐため、読み込み完了 or エラー時に一時URLを破棄
            img.onload = () => URL.revokeObjectURL(url);
            img.onerror = () => URL.revokeObjectURL(url);
        });
    });
</script>
@endsection