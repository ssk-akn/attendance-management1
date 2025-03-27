<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Attendance;
use App\Services\AttendanceCalculationService;

class ListController extends Controller
{
    public function getList(Request $request, AttendanceCalculationService $calcService)
    {
        $userId = Auth::id();
        $year = $request->input('year', now()->year);
        $month = $request->input('month', now()->month);
        $thisMonth = Carbon::create($year, $month, 1);
        $startOfMonth = $thisMonth->copy()->startOfDay();
        $endOfMonth = $thisMonth->copy()->endOfMonth()->endOfDay();

        $attendances = Attendance::where('user_id', $userId)
            ->whereBetween('date', [$startOfMonth, $endOfMonth])
            ->orderBy('date', 'asc')
            ->with(['breaks'])
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

        return view('list', compact('days', 'year', 'month'));
    }
}
