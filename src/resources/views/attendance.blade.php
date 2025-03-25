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
        {{ $now->isoFormat('YYYY年M月D日(ddd)') }}
    </div>
    <div class="attendance-time">
        {{ $now->isoFormat('HH:mm') }}
    </div>
    <div class="attendance-button">
        @if ($status === '勤務外')
        <form action="/attendance/work-start" method="POST">
            @csrf
            <button class="attendance-button__work" type="submit">
                出勤
            </button>
        </form>
        @elseif ($status === '出勤中')
        <form action="/attendance/work-end" method="POST">
            @csrf
            @method('PATCH')
            <button class="attendance-button__work" type="submit">
                退勤
            </button>
        </form>
        <form action="/attendance/break-start" method="POST">
            @csrf
            <button class="attendance-button__break" type="submit">
                休憩入
            </button>
        </form>
        @elseif ($status === '休憩中')
        <form action="/attendance/break-end" method="POST">
            @csrf
            @method('PATCH')
            <button class="attendance-button__break" type="submit">
                休憩戻
            </button>
        </form>
        @else
        <p class="attendance-comment">
            お疲れさまでした。
        </p>
        @endif
    </div>
</div>
@endsection