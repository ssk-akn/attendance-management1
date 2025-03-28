@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/detail.css') }}">
@endsection

@php
    use Carbon\Carbon;
@endphp

@section('content')
<div class="detail">
    <div class="detail-header">
        勤怠詳細
    </div>
    <form action="/attendance/detail" method="post" class="detail-form">
        <table class="detail-table">
            <tr class="detail-table__row">
                <th class="detail-table__header">名前</th>
                <td class="detail-table__item">
                    {{ $user->name }}
                </td>
            </tr>
            <tr class="detail-table__row">
                <th class="detail-table__header">日付</th>
                <td class="detail-table__item">
                    <p class="year">
                        {{ Carbon::parse($attendance->date)->isoFormat('YYYY年') }}
                    </p>
                    <p class="month">
                    {{ Carbon::parse($attendance->date)->isoFormat('M月D日') }}
                    </p>
                </td>
            </tr>
            <tr class="detail-table__row">
                <th class="detail-table__header">出勤・退勤</th>
                <td class="detail-table__item">
                    <input type="text" name="new_work_start" value="{{ Carbon::parse($attendance->work_start)->isoFormat('HH:mm') }}" class="time">
                    <span class="time-span">～</span>
                    <input type="text" name="new_work_end" value="{{ Carbon::parse($attendance->work_end)->isoFormat('HH:mm') }}" class="time">
                </td>
            </tr>
            @foreach ($attendance->breaks as $break)
            <tr class="detail-table__row">
                <th class="detail-table__header">休憩</th>
                <td class="detail-table__item">
                <input type="text" name="new_break_start" value="{{ Carbon::parse($break->break_start)->isoFormat('HH:mm') }}" class="time">
                    <span class="time-span">～</span>
                    <input type="text" name="new_break_end" value="{{ Carbon::parse($break->break_end)->isoFormat('HH:mm') }}" class="time">
                </td>
            </tr>
            @endforeach
            <tr class="detail-table__row">
                <th class="detail-table__header">備考</th>
                <td class="detail-table__item">
                    <textarea name="remarks" class="remarks"></textarea>
                </td>
            </tr>
        </table>
        <div class="detail-button">
            <button class="detail-button__submit" type="submit">修正</button>
        </div>
    </form>
</div>
@endsection