<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\AdminCorrectionRequest;

class DetailController extends Controller
{
    public function getDetail($id)
    {
        $attendance = Attendance::with('user', 'breaks')->findOrFail($id);
        $user = $attendance->user;
        $correction = Correction::where('attendance_id', $id)->latest('requested_at')->first();

        if (!$correction || $correction->status === '承認済み') {
            $correction = null;
        }

        $isAdmin = Auth::user()->isAdmin();

        return view('detail', compact('attendance', 'correction', 'user', 'isAdmin'));
    }

    public function update(AdminCorrectionRequest $request)
    {
        $attendance = Attendance::find($request->attendance_id);

        $attendance->update([
            'work_date' => Carbon::parse($request->work_date),
            'work_start' => $request->new_work_start,
            'work_end' => $request->new_work_end,
        ]);

        $existingBreaks = BreakTime::where('attendance_id', $attendance->id)->get();
        $breakStarts = $request->input('new_break_start', []);
        $breakEnds = $request->input('new_break_start', []);

        foreach ($breakStarts as $index => $start) {
            $end = $breakEnds[$index] ?? null;

            if ($index < $existingBreaks->count()) {
                $existingBreaks[$index]->update([
                    'break_start' => $start,
                    'break_end' => $end,
                ]);
            } elseif ($start && $end) {
                BreakTime::create([
                    'attendance_id' => $attendance->id,
                    'break_start' => $start,
                    'break_end' => $end,
                ]);
            }
        }
        return redirect()->route('admin.detail', ['id' => $attendance->id]);
    }
}
