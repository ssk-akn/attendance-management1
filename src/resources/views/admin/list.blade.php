@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/admin/list.css') }}">
@endsection

@section('content')
<div class="list">
    <div class="list-header">
        {{ $date->format('Y年m月d日の勤怠')}}
    </div>
    <div class="list-link">
        <a href="{{ route('admin.list', [
            'year' => $date->copy()->subDay()->year,
            'month' => $date->copy()->subDay()->month,
            'day' => $date->copy()->subDay()->day,
        ]) }}" class="list-link__item">
            <img src="{{ asset('image/left.png') }}" alt="←">
            <span class="previous-month">前日</span>
        </a>
        <p class="list-month">
            <img src="{{ asset('image/calendar.png') }}" alt="" class="calendar">
            <span class="this-month">{{ $date->format('Y/m/d') }}</span>
        </p>
        <a href="{{ route('admin.list', [
            'year' => $date->copy()->addDay()->year,
            'month' => $date->copy()->addDay()->month,
            'day' => $date->copy()->addDay()->day,
        ]) }}" class="list-link__item">
        <span class="previous-month">翌日</span>
        <img src="{{ asset('image/right.png') }}" alt="→">
        </a>
    </div>
    <table class="list-table">
        <tr class="list-table__row">
            <th class="list-table__header">名前</th>
            <th class="list-table__header">出勤</th>
            <th class="list-table__header">退勤</th>
            <th class="list-table__header">休憩</th>
            <th class="list-table__header">合計</th>
            <th class="list-table__header">詳細</th>
        </tr>
        @foreach ($attendances as $attendance)
        <tr class="list-table__row">
            <td class="list-table__item">
                {{ $attendance->user->name}}
            </td>
            <td class="list-table__item">
                {{ $attendance ? $attendance->formatted_start : '' }}
            </td>
            <td class="list-table__item">
                {{ $attendance && $attendance->work_end ? $attendance->formatted_end : '' }}
            </td>
            <td class="list-table__item">
                {{ $attendance->break_time }}
            </td>
            <td class="list-table__item">
                {{ $attendance->total_work_time }}
            </td>
            <td class="list-table__item">
                @if ($attendance)
                <a href="/attendance/{{ $attendance->id }}" class="detail-link">詳細</a>
                @endif
            </td>
        </tr>
        @endforeach
    </table>
</div>
@endsection