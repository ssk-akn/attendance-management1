<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

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

        return view('detail', compact('attendance', 'correction', 'user'));
    }

}
