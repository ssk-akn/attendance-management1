@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/admin/detail.css') }}">
@endsection

@section('content')
@php
    use Carbon\Carbon;
@endphp
<div class="detail">
    <div class="detail-header">
        勤怠詳細
    </div>
    <form action="{{ route('attendance.update') }}" method="POST" class="detail-form">
        @csrf
        @method('PUT')
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
                    <label class="date-label">
                        <input type="date" name="date" value="{{ old('date', $attendance->date) }}" class="date">
                    </label>
                </td>
            </tr>
            <tr class="detail-table__row">
                <th class="detail-table__header">出勤・退勤</th>
                <td class="detail-table__item">
                        <input type="text" name="new_work_start" value="{{ old('new_work_start', $attendance->formatted_start) }}" class="time">
                        <span class="time-span">～</span>
                        <input type="text" name="new_work_end" value="{{ old('new_work_end', $attendance->formatted_end) }}" class="time">
                    @error('new_work_start')
                        <p class="error">{{ $message }}</p>
                    @enderror
                    @error('new_work_end')
                        <p class="error">{{ $message }}</p>
                    @enderror
                </td>
            </tr>
            @foreach ($attendance->breaks as $index => $break)
            <tr class="detail-table__row">
                <th class="detail-table__header">
                    {{ $loop->first ? '休憩' : '休憩' . ($index + 1) }}
                </th>
                <td class="detail-table__item">
                    <input type="text" name="new_break_start[]" value="{{ old('new_break_start.$index', Carbon::parse($break['break_start'])->isoFormat('HH:mm')) }}" class="time">
                    <span class="time-span">～</span>
                    <input type="text" name="new_break_end[]" value="{{ old('new_break_end.$index', Carbon::parse($break['break_end'])->isoFormat('HH:mm')) }}" class="time">
                    @error("new_break_start.$index")
                    <p class="error">{{ $message }}</p>
                    @enderror
                </td>
            </tr>
            @endforeach
            @php
                $breaksCount = count($attendance->breaks);
            @endphp
            <tr class="detail-table__row">
                <th class="detail-table__header">
                    {{ '休憩' . ($breaksCount + 1) }}
                </th>
                <td class="detail-table__item">
                    <input type="text" name="new_break_start[]" value="{{ old('new_break_start.$breaksCount') }}" class="time">
                    <span class="time-span">～</span>
                    <input type="text" name="new_break_end[]" value="{{ old('new_break_end.$breaksCount') }}" class="time">
                </td>
            </tr>
            <tr class="detail-table__row">
                <th class="detail-table__header">備考</th>
                <td class="detail-table__item">
                        <textarea name="remarks" class="remarks">{{ old('remarks') }}</textarea>
                    @error('remarks')
                        <p class="error">{{ $message }}</p>
                    @enderror
                </td>
            </tr>
        </table>
        <div class="detail-button">
                <button class="detail-button__submit" type="submit">修正</button>
        </div>
        <input type="hidden" name="user_id" value="{{ $user->id }}">
        <input type="hidden" name="attendance_id" value="{{ $attendance->id }}">
    </form>
</div>
@endsection