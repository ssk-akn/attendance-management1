<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>COACHTECH</title>
    <link rel="stylesheet" href="{{ asset('css/sanitize.css') }}">
    <link rel="stylesheet" href="{{ asset('css/common.css') }}">
    @yield('css')
</head>
<body>
    <div class="app">
        <header class="header">
            <a class="header-link" href="/login">
                <img src="{{ asset('image/logo.svg') }}" alt="COACHTECH" class="header-img">
            </a>
            @if (Auth::check())
            <ul class="header-nav">
                @if (Auth::user()->isAdmin())
                <li class="header-nav__item">
                    <a href="{{ route('admin.list') }}" class="header-nav__link">勤怠一覧</a>
                </li>
                <li class="header-nav__item">
                    <a href="/admin/staff/list" class="header-nav__link">スタッフ一覧</a>
                </li>
                <li class="header-nav__item">
                    <a href="/stamp_correction_request/list" class="header-nav__link">申請一覧</a>
                </li>
                @else
                <li class="header-nav__item">
                    <a href="/attendance" class="header-nav__link">勤怠</a>
                </li>
                <li class="header-nav__item">
                    <a href="/attendance/list" class="header-nav__link">勤怠一覧</a>
                </li>
                <li class="header-nav__item">
                    <a href="/stamp_correction_request/list" class="header-nav__link">申請</a>
                </li>
                @endif
                <li class="header-nav__item">
                    <form action="/logout" method="post" class="logout-form">
                        @csrf
                        <button class="logout-button">ログアウト</button>
                    </form>
                </li>
            </ul>
            @endif
        </header>
        <div class="content">
            @yield('content')
        </div>
    </div>
</body>
</html>