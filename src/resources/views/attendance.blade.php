@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/attendance.css') }}">
@endsection

@section('content')
<div class="attendance-form">
    <div class="attendance-status">
        {{ $status }}
    </div>
    <div class="attendance-date">
        
    </div>
    <div class="attendance-time">

    </div>
    <div class="attendance-form__button">
        @if ($status === '勤務外')
        <button class="attendance-button__work-start" type="submit">
            出勤
        </button>
        @elseif ($status === '勤務中')
        <button class="attendance-button__work-end" type="submit">
            退勤
        </button>
        <button class="attendance-button__break-start" type="submit">
            休憩入
        </button>
        @elseif ($status === '休憩中')
        <button class="attendance-button__break-end" type="submit">
            休憩戻
        </button>
        @else
        <p class="attendance-form__comment">
            お疲れさまでした。
        </p>
    </div>
</div>