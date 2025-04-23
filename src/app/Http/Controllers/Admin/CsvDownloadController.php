<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;
use App\Services\AttendanceCalculationService;
use Carbon\Carbon;
use App\Models\Attendance;

class CsvDownloadController extends Controller
{
    public function downloadCsv(Request $request, AttendanceCalculationService $calcService)
    {
        $year = $request->input('year', now()->year);
        $month = $request->input('month', now()->month);
        $thisMonth = Carbon::create($year, $month, 1);
        $startOfMonth = $thisMonth->copy()->startOfDay();
        $endOfMonth = $thisMonth->copy()->endOfMonth()->endOfDay();

        $attendances = Attendance::with('breaks')
            ->where('user_id', $request->user_id)
            ->whereBetween('date', [$startOfMonth, $endOfMonth])
            ->orderBy('date', 'asc')
            ->get()
            ->map(function ($attendance) use ($calcService) {
                $attendance->break_time = $calcService->calculateBreakTime($attendance->breaks);
                $attendance->total_work_time = $calcService->calculateWorkTime(
                    $attendance->work_start,
                    $attendance->work_end,
                    $attendance->breaks
                );
                return $attendance;
            });

        $csvHeader = ['日付', '出勤', '退勤', '休憩', '合計'];

        $csvDate = $attendances->map(function ($a) {
            return [
                '="' . $a->date . '"',
                $a->work_start ?? '-',
                $a->work_end ?? '-',
                $a->break_time ?? '00:00',
                $a->total_work_time ?? '00:00',
            ];
        })->toArray();

        $response = new StreamedResponse(function () use ($csvHeader, $csvDate) {
            $handle = fopen('php://output', 'w');
            fwrite($handle, "\xEF\xBB\xBF");
            fputcsv($handle, $csvHeader);

            foreach ($csvDate as $row) {
                fputcsv($handle, $row);
            }

            fclose($handle);
        }, 200, [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="attendance_' . $year . '_' . $month . '.csv"',
        ]);

        return $response;
    }
}
