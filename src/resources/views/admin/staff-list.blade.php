@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/admin/staff.css') }}">
@endsection

@section('content')
<div class="staff">
    <div class="staff-header">スタッフ一覧</div>
    <table class="table">
        <tr class="staff-table__row">
            <th class="staff-table__header">名前</th>
            <th class="staff-table__header">メールアドレス</th>
            <th class="staff-table__header">月次勤怠</th>
        </tr>
        @foreach ($users as $user)
        <tr class="staff-table__row">
            <td class="staff-table__item">
                {{ $user->name }}
            </td>
            <td class="staff-table__item">
                {{ $user->email }}
            </td>
            <td class="staff-table__item">
                <a class="staff-link" href="/admin/attendance/staff/{{ $user->id }}">詳細</a>
            </td>
        </tr>
        @endforeach
    </table>
</div>
@endsection