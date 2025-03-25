@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/list.css') }}">
@endsection

@php
    use Carbon\Carbon;
@endphp

@section('content')
<div class="list">
    <div class="list-header">
        勤怠一覧
    </div>
    <div class="list-link">
        <a href="{{ route('attendance.list', [
            'year' => Carbon::create($year, $month, 1)->subMonth()->year,
            'month' => Carbon::create($year, $month, 1)->subMonth()->month,
        ]) }}" class="list-link__item">
            <img src="{{ asset('image/left.png') }}" alt="←" class="previous-arrow">
            <span class="previous-month">前月</span>
        </a>
        <p class="list-month">
            <img src="{{ asset('image/calendar.png') }}" alt="" class="calendar">
            <span class="this-month">{{ $year . '/' . $month }}</span>
        </p>
        <a href="{{ route('attendance.list', [
            'year' => Carbon::create($year, $month, 1)->addMonth()->year,
            'month' => Carbon::create($year, $month, 1)->addMonth()->month,
        ]) }}" class="next-month">
            <img src="{{ asset('image/right.png') }}" alt="→" class="previous-arrow">
            <span class="previous-month">前月</span>
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
        @foreach ($attendances as $attendance)
        <tr class="list-table__row">
            <td class="list-table__item">
                {{ Carbon::parse($attendance->date)->isoFormat('M/D(ddd)') }}
            </td>
            <td class="list-table__item">
                {{ Carbon::parse($attendance->work_start)->isoFormat('HH:mm')}}
            </td>
            <td class="list-table__item">
                {{ Carbon::parse($attendance->work_end)->isoFormat('HH:mm')}}
            </td>
            <td class="list-table__item"></td>
            <td class="list-table__item"></td>
            <td class="list-table__item"></td>
        </tr>
        @endforeach
    </table>
</div>
@endsection