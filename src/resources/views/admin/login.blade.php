@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/admin/login.css') }}">
@endsection

@section('content')
<div class="login-form">
    <div class="login-form__heading">管理者ログイン</div>
    <div class="login-form__inner">
        <form class="login-form__form" action="/admin/login" method="post">
            @csrf
            <div class="login-form__group">
                <label for="email" class="login-form__label">メールアドレス</label>
                <input type="text" name="email" id="email" class="login-form__input" value="{{ old('email') }}">
                @error('email')
                <p class="login-form__error-message">
                    {{ $message }}
                </p>
                @enderror
            </div>
            <div class="login-form__group">
                <label for="password" class="login-form__label">パスワード</label>
                <input type="password" name="password" id="password" class="login-form__input">
                @error('password')
                <p class="login-form__error-message">
                    {{ $message }}
                </p>
                @enderror
            </div>
            <div class="login-form__button">
                <button class="login-form__button-submit">管理者ログインする</button>
            </div>
        </form>
    </div>
</div>
@endsection