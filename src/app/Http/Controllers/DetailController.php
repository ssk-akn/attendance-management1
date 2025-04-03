<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\Attendance;
use App\Models\Correction;
use App\Http\Requests\CorrectionRequest;

class DetailController extends Controller
{
    public function getDetail($id)
    {
        $user = Auth::user();
        $attendance = Attendance::with('breaks')->findOrFail($id);
        $correction = Correction::where('attendance_id', $id)->latest('requested_at')->first();

        return view('detail', compact('user', 'attendance', 'correction'));
    }

    public function create(CorrectionRequest $request)
    {
        $userId = $request->user_id;
        $attendanceId = $request->attendance_id;

        $breakStarts = $request->input('new_break_start', []);
        $breakEnds = $request->input('new_break_end', []);
        $new_breaks = [];

        foreach ($breakStarts as $index => $startTime) {
            if (isset($breakEnds[$index])) {
                $new_breaks[] = [
                    'start' => $startTime,
                    'end' => $breakEnds[$index]
                ];
            }
        }

        $correction = new Correction();
        $correction->attendance_id = $attendanceId;
        $correction->user_id = $userId;
        $correction->new_work_start = $request->new_work_start;
        $correction->new_work_end = $request->new_work_end;
        $correction->new_breaks = $new_breaks;
        $correction->remarks = $request->remarks;
        $correction->save();

        return redirect('/attendance/list');
    }
}
