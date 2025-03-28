<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\Attendance;

class DetailController extends Controller
{
    public function getDetail($id)
    {
        $user = Auth::user();
        $attendance = Attendance::with('breaks')
            ->findOrFail($id);

        return view('detail', compact('user', 'attendance'));
    }
}
