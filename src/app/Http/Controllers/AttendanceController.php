<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Attendance;
use App\Models\BreakTime;
use Carbon\Carbon;

class AttendanceController extends Controller
{
    public function getAttendance()
    {
        $userId = Auth::id();
        $now = Carbon::now();

        $attendance = Attendance::where('user_id', $userId)
            ->where('date', $now->toDateString())
            ->first();

        $status = '勤務外';
        if ($attendance) {
            if($attendance->work_end) {
                $status = '退勤済';
            } elseif ($attendance->work_start) {
                $latestBreak = BreakTime::where('attendance_id', $attendance->id)
                    ->whereNull('break_end')
                    ->latest('break_start')
                    ->first();
                $status = $latestBreak ? '休憩中' : '出勤中';
            }
        }

        return view('attendance', compact('userId', 'status', 'now'));
    }

    public function workStart()
    {
        $userId = Auth::id();
        $now = Carbon::now();

        $existingAttendance = Attendance::where('user_id', $userId)
            ->where('date', $now->toDateString())
            ->first();

        if ($existingAttendance) {
            return redirect()->back();
        }

        $attendance = new Attendance();
        $attendance->user_id = $userId;
        $attendance->date = $now->toDateString();
        $attendance->work_start = $now->toTimeString();
        $attendance->save();

        return redirect()->back();
    }

    public function breakStart()
    {
        $userId = Auth::id();
        $now = Carbon::now();

        $attendance = Attendance::where('user_id', $userId)
            ->where('date', $now->toDateString())
            ->first();

        if ($attendance) {
            $breakTime = new BreakTime();
            $breakTime->attendance_id = $attendance->id;
            $breakTime->break_start = $now->toTimeString();
            $breakTime->save();
        }

        return redirect()->back();
    }

    public function breakEnd()
    {
        $userId = Auth::id();
        $now = Carbon::now();

        $attendance = Attendance::where('user_id', $userId)
            ->where('date', $now->toDateString())
            ->first();

        $latestBreak = BreakTime::where('attendance_id', $attendance->id)
            ->whereNull('break_end')
            ->latest('break_start')
            ->first();

        $latestBreak->break_end = $now->toTimeString();
        $latestBreak->save();

        return redirect()->back();
    }

    public function workEnd()
    {
        $userId = Auth::id();
        $now = Carbon::now();

        $attendance = Attendance::where('user_id', $userId)
            ->where('date', $now->toDateString())
            ->whereNull('work_end')
            ->latest('work_start')
            ->first();

        $attendance->work_end = $now->toTimeString();
        $attendance->save();

        return redirect()->back();
    }
}
