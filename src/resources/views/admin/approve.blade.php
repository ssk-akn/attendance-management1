@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/admin/approve.css') }}">
@endsection

@section('content')
<div class="approve">
    <div class="approve-header">
        勤怠詳細
    </div>
    <table class="approve-table">
        <tr class="approve-table__row">
            <th class="approve-table__header">名前</th>
            <td class="approve-table__item">
                <div class="name">{{ $correction->user->name }}</div>
            </td>
        </tr>
        <tr class="approve-table__row">
            <th class="approve-table__header">日付</th>
            <td class="approve-table__item">
                <div class="date-wrap">
                    <div class="year">{{ $attendance->formatted_year }}</div>
                    <div class="month">{{ $attendance->formatted_date }}</div>
                </div>
            </td>
        </tr>
        <tr class="approve-table__row">
            <th class="approve-table__header">出勤・退勤</th>
            <td class="approve-table__item">
                <div class="correction-time">
                    {{ $correction->formatted_start }}
                    <span class="time-span">～</span>
                    {{ $correction->formatted_end }}
                </div>
            </td>
        </tr>
        @if(is_array($correction->new_breaks))
        @foreach ($correction->new_breaks as $index => $break)
        <tr class="approve-table__row">
            <th class="approve-table__header">
                {{ $loop->first ? '休憩' : '休憩' . ($index + 1) }}
            </th>
            <td class="approve-table__item">
                <div class="correction-time">
                    {{ $break['start'] }}
                    <span class="time-span">～</span>
                    {{ $break['end'] }}
                </div>
            </td>
        </tr>
        @endforeach
        @endif
        <tr class="approve-table__row">
            <th class="approve-table__header">備考</th>
            <td class="approve-table__item">
                <div class="correction-remarks">{{ $correction->remarks }}</div>
            </td>
        </tr>
    </table>
    <form action="/stamp_correction_request/approve/update" method="post">
        @csrf
        @method ('PUT')
        <input type="hidden" name="correction_id" value="{{ $correction->id }}">
        <input type="hidden" name="attendance_id" value="{{ $attendance->id }}">
        <div class="approve-button">
            @if ($correction->status === '承認待ち')
                <button class="approve-button__submit" type="submit">承認</button>
            @else
                <div class="approved-button">承認済み</div>
            @endif
        </div>
    </form>
</div>
@endsection