@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/admin/attendance-staff.css') }}">
@endsection

@php
    use Carbon\Carbon;
@endphp

@section('content')
<div class="attendance-list">
    <div class="attendance-list__header">
        {{ $user->name . 'さんの勤怠' }}
    </div>
    <div class="list-link">
        <!-- 前月リンク -->
        <a
            href="{{ route('admin.attendance', [
                'id' => $user->id,
                'year' => Carbon::create($year, $month, 1)->copy()->subMonth()->year,
                'month' => Carbon::create($year, $month, 1)->copy()->subMonth()->month,
            ]) }}"
            class="list-link__item">
            <img src="{{ asset('image/left.png') }}" alt="←">
            <span class="previous-month">前月</span>
        </a>
        <!-- 現在の月 -->
        <p class="list-month">
            <img src="{{ asset('image/calendar.png') }}" alt="" class="calendar">
            <span class="this-month">{{ $year . '/' . sprintf('%02d', $month) }}</span>
        </p>
        <!-- 翌月のリンク -->
        <a
            href="{{ route('admin.attendance', [
                'id' => $user->id,
                'year' => Carbon::create($year, $month, 1)->copy()->addMonth()->year,
                'month' => Carbon::create($year, $month, 1)->copy()->addMonth()->month,
            ]) }}"
            class="list-link__item">
            <span class="previous-month">翌月</span>
            <img src="{{ asset('image/right.png') }}" alt="→">
        </a>
    </div>
    <table class="list-table">
        <tr class="list-table__row">
            <th class="list-table__header">日付</th>
            <th class="list-table__header">出勤</th>
            <th class="list-table__header">退勤</th>
            <th class="list-table__header">休憩</th>
            <th class="list-table__header">合計</th>
            <th class="list-table__header">詳細</th>
        </tr>
        @foreach ($days as $date => $attendance)
        <tr class="list-table__row">
            <td class="list-table__item">
                {{ Carbon::parse($date)->isoFormat('MM/DD(ddd)') }}
            </td>
            <td class="list-table__item">
                {{ $attendance ? $attendance->formatted_start : '' }}
            </td>
            <td class="list-table__item">
                {{ $attendance && $attendance->work_end ? $attendance->formatted_end : '' }}
            </td>
            <td class="list-table__item">
                {{ $attendance && $attendance->break_time ? $attendance->break_time : '' }}
            </td>
            <td class="list-table__item">
                {{ $attendance && $attendance->total_work_time ? $attendance->total_work_time : '' }}
            </td>
            <td class="list-table__item">
                @if ($attendance)
                <a href="/attendance/{{ $attendance->id }}" class="detail-link">詳細</a>
                @endif
            </td>
        </tr>
        @endforeach
    </table>
    <form action="/attendance/csv" method="POST" class="csv-form">
        @csrf
        <input type="hidden" name="user_id" value="{{ $user->id }}">
        <div class="csv-button">
            <button class="csv-button__submit" type="submit">CSV出力</button>
        </div>
    </form>
</div>
@endsection