@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/detail.css') }}">
@endsection

@section('content')
<div class="detail">
    <div class="detail-header">
        勤怠詳細
    </div>
    <form action="/attendance/detail" method="post" class="detail-form">
        @csrf
        @php $waitApproval = optional($correction)->status === '承認待ち'; @endphp
        <table class="detail-table">
            <tr class="detail-table__row">
                <th class="detail-table__header">名前</th>
                <td class="detail-table__item">
                    <div class="name">{{ $user->name }}</div>
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
                    @if($waitApproval)
                        <div class="work-time">
                            <div class="work-start">{{ $correction->formatted_start }}</div>
                            <span class="correction-span">～</span>
                            <div class="work-end">{{ $correction->formatted_end }}</div>
                        </div>
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
                        @if($waitApproval)
                            <div class="break-time">
                                <div class="break-start">{{ $break['start'] }}</div>
                                <span class="correction-span">～</span>
                                <div class="break-end">{{ $break['end'] }}</div>
                            </div>
                        @else
                            <input type="text" name="new_break_start[]" value="{{ $break->formatted_start }}" class="time">
                            <span class="time-span">～</span>
                            <input type="text" name="new_break_end[]" value="{{ $break->formatted_end }}" class="time">
                        @endif
                    </td>
                    @error('new_break_start.$index')
                    <p class="error">{{ $message }}</p>
                    @enderror
                </tr>
                @endforeach
            <tr class="detail-table__row">
                <th class="detail-table__header">備考</th>
                <td class="detail-table__item">
                    @if($waitApproval)
                        <div class="remarks">{{ $correction->remarks }}</div>
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
            @if ($waitApproval)
            <p class="correction-message">
                *承認待ちのため修正はできません。
            </p>
            @else
            <button class="detail-button__submit" type="submit">修正</button>
            @endif
        </div>
        <input type="hidden" name="user_id" value="{{ $user->id }}">
        <input type="hidden" name="attendance_id" value="{{ $attendance->id }}">
    </form>
</div>
@endsection