@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/detail.css') }}">
@endsection

@section('content')
@php
    $isAdmin = Auth::user()->isAdmin();
    use Carbon\Carbon;
@endphp
<div class="detail">
    <div class="detail-header">
        勤怠詳細
    </div>
    <form action="{{ $isAdmin ? route('attendance.update') : route('attendance.correction') }}" method="post" class="detail-form">
        @csrf
        @if ($isAdmin)
            @method ('PUT')
        @endif
        <table class="detail-table">
            <tr class="detail-table__row">
                <th class="detail-table__header">名前</th>
                <td class="detail-table__item">
                    <div class="name">{{ $attendance->user->name }}</div>
                </td>
            </tr>
            <tr class="detail-table__row">
                <th class="detail-table__header">日付</th>
                <td class="detail-table__item">
                    <div class="date-wrap">
                        <div class="year">{{ $attendance->formatted_year }}</div>
                        <div class="month">{{ $attendance->formatted_date }}</div>
                    </div>
                </td>
            </tr>
            <tr class="detail-table__row">
                <th class="detail-table__header">出勤・退勤</th>
                <td class="detail-table__item">
                    @if($correction)
                        <input type="text" name="new_work_start" value="{{ $correction->formatted_start }}" readonly class="time">
                        <span class="time-span">～</span>
                        <input type="text" name="new_work_end" value="{{ $correction->formatted_end }}" readonly class="time">
                    @else
                        <input type="text" name="new_work_start" value="{{ $attendance->formatted_start }}" class="time">
                        <span class="time-span">～</span>
                        <input type="text" name="new_work_end" value="{{ $attendance->formatted_end }}" class="time">
                    @endif
                    @error('new_work_start')
                        <p class="error">{{ $message }}</p>
                    @enderror
                    @error('new_work_end')
                        <p class="error">{{ $message }}</p>
                    @enderror
                </td>
            </tr>
            @foreach ($correction ? $correction->new_breaks : $attendance->breaks as $index => $break)
            <tr class="detail-table__row">
                <th class="detail-table__header">
                    {{ $loop->first ? '休憩' : '休憩' . ($index + 1) }}
                </th>
                <td class="detail-table__item">
                    @if($correction)
                        <input type="text" name="new_break_start[]" value="{{ $break['start'] }}" readonly class="time">
                        <span class="time-span">～</span>
                        <input type="text" name="new_break_start[]" value="{{ $break['end'] }}" readonly class="time">
                    @else
                        <input type="text" name="new_break_start[]" value="{{ Carbon::parse($break['start'])->isoFormat('HH:mm') }}" class="time">
                        <span class="time-span">～</span>
                        <input type="text" name="new_break_end[]" value="{{ Carbon::parse($break['end'])->isoFormat('HH:mm') }}" class="time">
                    @endif
                </td>
                @error('new_break_start.$index')
                <p class="error">{{ $message }}</p>
                @enderror
            </tr>
            @endforeach
            @php
                $breaksCount = count($attendance->breaks);
            @endif
            @if ($isAdmin && is_null($correction))
            <tr class="detail-table__row">
                <th class="detail-table__header">
                    {{ '休憩' . ($breaksCount + 1) }}
                </th>
                <td class="detail-table__item">
                    <input type="text" name="new_break_start[]" class="time">
                    <span class="time-span">～</span>
                    <input type="text" name="new_break_end[]" class="time">
                </td>
            </tr>
            @endif
            <tr class="detail-table__row">
                <th class="detail-table__header">備考</th>
                <td class="detail-table__item">
                    @if($correction)
                        <textarea name="remarks" readonly class="remarks">{{ $correction->remarks }}</textarea>
                    @else
                        <textarea name="remarks"></textarea>
                    @endif
                    @error('remarks')
                        <p class="error">{{ $message }}</p>
                    @enderror
                </td>
            </tr>
        </table>
        <div class="detail-button">
            @if ($correction)
                @if ($isAdmin)
                <button class="detail-button__submit" type="submit">承認</button>
                @else
                <p class="correction-message">
                    *承認待ちのため修正はできません。
                </p>
                @endif
            @else
                <button class="detail-button__submit" type="submit">修正</button>
            @endif
        </div>
        <input type="hidden" name="user_id" value="{{ $user->id }}">
        <input type="hidden" name="attendance_id" value="{{ $attendance->id }}">
    </form>
</div>
@endsection