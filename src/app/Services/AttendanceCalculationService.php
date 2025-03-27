<?php

namespace App\Services;

use Carbon\Carbon;

class AttendanceCalculationService
{
    public function calculateBreakTime($breaks)
    {
        $totalBreakMinutes = $breaks->sum(function ($break) {
            return Carbon::parse($break->break_end)->diffInMinutes(Carbon::parse($break->break_start));
        });

        $hours = floor($totalBreakMinutes / 60);
        $minutes = $totalBreakMinutes % 60;

        return sprintf('%02d:%02d', $hours, $minutes);
    }

    public function calculateWorkTime($workStart, $workEnd, $breaks)
    {
        $totalBreakMinutes = $breaks->sum(function ($break) {
            return Carbon::parse($break->break_end)->diffInMinutes(Carbon::parse($break->break_start));
        });

        $workStartTime = Carbon::parse($workStart);
        $workEndTime = Carbon::parse($workEnd);
        $workMinutes = $workEndTime->diffInMinutes($workStartTime) - $totalBreakMinutes;

        $hours = floor($workMinutes / 60);
        $minutes = $workMinutes % 60;

        return sprintf('%02d:%02d', $hours, $minutes);
    }
}