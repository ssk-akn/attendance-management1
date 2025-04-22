<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\AdminCorrectionRequest;
use Carbon\Carbon;
use App\Models\Attendance;
use App\Models\Correction;
use App\Models\BreakTime;

class DetailController extends Controller
{
    public function update(AdminCorrectionRequest $request)
    {
        $attendance = Attendance::find($request->attendance_id);

        $attendance->update([
            'date' => $request->date,
            'work_start' => $request->new_work_start,
            'work_end' => $request->new_work_end,
        ]);

        $existingBreaks = BreakTime::where('attendance_id', $attendance->id)->get();
        $breakStarts = $request->input('new_break_start', []);
        $breakEnds = $request->input('new_break_end', []);

        foreach ($existingBreaks as $index => $break) {
            $start = $breakStarts[$index] ?? null;
            $end = $breakEnds[$index] ?? null;

            if (empty($start)) {
                $break->delete();
            } else {
                $break->update([
                    'break_start' => $start,
                    'break_end' => $end,
                ]);
            }
        }

        if (count($breakStarts) > $existingBreaks->count()) {
            $index = $existingBreaks->count();
            $start = $breakStarts[$index] ?? null;
            $end = $breakEnds[$index] ?? null;

            if(!empty($start)) {
                BreakTime::create([
                    'attendance_id' => $attendance->id,
                    'break_start' => $start,
                    'break_end' => $end,
                ]);
            }
        }

        $new_breaks = [];
        foreach ($breakStarts as $index => $startTime) {
            if (isset($breakEnds[$index])) {
                $new_breaks[] = [
                    'start' => $startTime,
                    'end' => $breakEnds[$index]
                ];
            }
        }

        $correction = new Correction;
        $correction->attendance_id = $request->attendance_id;
        $correction->user_id = $request->user_id;
        $correction->new_work_start = $request->new_work_start;
        $correction->new_work_end = $request->new_work_end;
        $correction->new_breaks = $new_breaks;
        $correction->remarks = $request->remarks;
        $correction->status = '修正';
        $correction->save();

        return redirect()->route('attendance.detail', ['id' => $attendance->id]);
    }
}
