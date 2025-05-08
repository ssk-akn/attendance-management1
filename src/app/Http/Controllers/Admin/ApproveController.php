<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Correction;
use App\Models\Attendance;
use App\Models\BreakTime;

class ApproveController extends Controller
{
    public function getCorrection($attendance_correct_request)
    {
        $correction = Correction::with('user', 'attendance')
            ->find($attendance_correct_request);

            $attendance = $correction->attendance;

        return view('admin.approve', compact('correction', 'attendance'));
    }

    public function update(Request $request)
    {
        $correction = Correction::findOrFail($request->correction_id);

        DB::transaction(function () use ($correction, $request) {
            $attendance = Attendance::findOrFail($request->attendance_id);

            $attendance->work_start = $correction->new_work_start;
            $attendance->work_end = $correction->new_work_end;
            $attendance->save();

            BreakTime::where('attendance_id', $attendance->id)->delete();

            foreach ($correction->new_breaks ?? [] as $break) {
                BreakTime::create([
                    'attendance_id' => $attendance->id,
                    'break_start' => $break['start'],
                    'break_end' => $break['end'],
                ]);
            }

            $correction->status = '承認済み';
            $correction->save();
            });

        return redirect()->route('admin.correction', ['attendance_correct_request' => $correction->id]);
    }
}
