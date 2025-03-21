<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Attendance;
use App\Models\BreakTime;

class AttendanceController extends Controller
{
    public function getAttendance()
    {
        $userId = Auth::id();

        $attendance = Attendance::where('user_id', $userId)
            ->latest()->first();

        $status = '勤務外';
        if ($attendance) {
            if($attendance->work_end) {
                $status = '退勤済';
            } elseif ($attendance->work_start) {
                $latestBreak = BreakTime::where('user_id', $userId)
                    ->whereNull('break_end')
                    ->latest('break_start')
                    ->first();
                $status = $latestBreak ? '休憩中' : '出勤中';
            }
        }
        return view('attendance', compact('userId', 'status', 'attendance'));
    }
}
