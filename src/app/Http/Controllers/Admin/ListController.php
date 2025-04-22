<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Models\Attendance;
use App\Services\AttendanceCalculationService;

class ListController extends Controller
{
    public function getAttendanceList(Request $request, AttendanceCalculationService $calcService)
    {
        $year = $request->input('year', now()->year);
        $month = $request->input('month', now()->month);
        $day = $request->input('day', now()->day);

        $date = Carbon::create($year, $month, $day);

        $attendances = Attendance::where('date', $date)
            ->with('user')
            ->with(['breaks'])
            ->get()
            ->map(function ($attendance) use ($calcService) {
                $attendance->break_time = $calcService->calculateBreakTime($attendance->breaks);
                $attendance->total_work_time = $calcService->calculateWorkTime($attendance->work_start, $attendance->work_end, $attendance->breaks);
                return $attendance;
            });

        return view('admin.list', compact('date', 'attendances'));
    }
}
