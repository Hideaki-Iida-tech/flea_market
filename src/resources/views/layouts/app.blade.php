<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title')</title>
    <link rel="stylesheet" href="{{ asset('css/layouts/sanitize.css') }}">
    <link rel="stylesheet" href="{{ asset('css/layouts/common.css') }}">
    @yield('css')
</head>

<body>
    <header class="header">
        <div class="header__inner">
            <div class="header__logo">
                <img src="{{ asset('images/logo.svg') }}" alt="coachtech" />
            </div>
            <div class="header__input">
                @yield('input')
            </div>
            <div class="header__button">
                @yield('button')
            </div>
        </div>
    </header>
    <main>
        @yield('content')
    </main>
</body>

</html>