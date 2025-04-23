@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/verify-email.css') }}">
@endsection

@section('content')
<div class="verify-page">
    <div class="verify-message">
        登録いただいたメールアドレスに認証メールを送付しました。</br>
        メール認証を完了してください。
    </div>
    @if (session('status') == 'verification-link-sent')
    <div class="completion-message">
        認証メールが送信されました。
    </div>
    @endif
    <a href="http://localhost:8025/" class="verify-link">認証はこちらから</a>
    <form action="{{ route('verification.send') }}" method="POST" class="verify-form">
        @csrf
        <button class="verification-mail__button" type="submit">
            認証メールを再送する
        </button>
    </form>
</div>
@endsection