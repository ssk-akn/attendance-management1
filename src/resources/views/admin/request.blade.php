@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/request.css') }}">
@endsection

@section('content')
<div class="request-list">
    <div class="request-list__header">申請一覧</div>
    <div class="request-status">
        <div class="request-status__wait">
            <a
                href="{{ url('/stamp_correction_request/list/?page=wait') }}"
                class="{{ $page === 'wait' ? 'active' : 'passive' }}">
                承認待ち
            </a>
        </div>
        <div class="request-status__approved">
            <a
                href="{{ url('/stamp_correction_request/list/?page=approved') }}"
                class="{{ $page === 'approved' ? 'active' : 'passive' }}">
                承認済み
            </a>
        </div>
    </div>
    <table class="request-table">
        <tr class="request-table__row">
            <th class="request-table__header">状態</th>
            <th class="request-table__header">名前</th>
            <th class="request-table__header">対象日時</th>
            <th class="request-table__header">申請理由</th>
            <th class="request-table__header">申請日時</th>
            <th class="request-table__header">詳細</th>
        </tr>
        @foreach ($corrections as $correction)
        <tr class="request-table__row">
            <td class="request-table__item">{{ $correction->status }}</td>
            <td class="request-table__item">{{ $correction->user->name }}</td>
            <td class="request-table__item">{{ $correction->attendance->formatted_stamp }}</td>
            <td class="request-table__item">{{ $correction->remarks }}</td>
            <td class="request-table__item">{{ $correction->formatted_date }}</td>
            <td class="request-table__item">
                <a
                    href="/stamp_correction_request/approve/{{ $correction->id }}"
                    class="request-table__detail">
                    詳細
                </a>
            </td>
        </tr>
        @endforeach
    </table>
</div>
@endsection
