<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Attendance;
use App\Services\AttendanceCalculationService;
use Carbon\Carbon;

class StaffController extends Controller
{
    public function getStaffList()
    {
        $users = User::where('role', 'user')->get();

        return view('admin.staff-list', compact('users'));
    }

    public function getAttendance(Request $request, AttendanceCalculationService $calcService)
    {
        $user = User::find($request->id);
        $year = $request->input('year', now()->year);
        $month = $request->input('month', now()->month);
        $thisMonth = Carbon::create($year, $month, 1);
        $startOfMonth = $thisMonth->copy()->startOfDay();
        $endOfMonth = $thisMonth->copy()->endOfMonth()->endOfDay();

        $attendances = Attendance::with('user', 'breaks')
            ->where('user_id', $request->id)
            ->whereBetween('date', [$startOfMonth, $endOfMonth])
            ->orderBy('date', 'asc')
            ->get()
            ->map(function ($attendance) use ($calcService) {
                $attendance->break_time = $calcService->calculateBreakTime($attendance->breaks);
                $attendance->total_work_time = $calcService->calculateWorkTime($attendance->work_start, $attendance->work_end, $attendance->breaks);
                return $attendance;
            });

        $days = [];
        for ($day = 1; $day <= $thisMonth->daysInMonth; $day++) {
            $dateKey = Carbon::create($year, $month, $day)->toDateString();
            $days[$dateKey] = null;
        }

        foreach ($attendances as $attendance) {
            $days[$attendance->date] = $attendance;
        }

        return view('admin.attendance-staff', compact('user', 'year', 'month', 'days'));
    }
}
