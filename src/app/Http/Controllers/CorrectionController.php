<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Correction;

class CorrectionController extends Controller
{
    public function getRequestList(Request $request)
    {
        $user = Auth::user();
        $page = request('page', 'wait');

        if ($request->attributes->get('is_admin')) {
            if ($page === 'approved') {
                $corrections = Correction::with('attendance', 'user')
                ->where('status', '承認済み')
                ->get();
            } else {
                $corrections = Correction::with('attendance', 'user')
                ->where('status', '承認待ち')
                ->get();
            }
            return view('admin.request', compact(['corrections', 'page']));
        } else {
            if ($page === 'approved') {
                $corrections = Correction::with('attendance')
                ->where('user_id', $user->id)
                ->where('status', '承認済み')
                ->get();
            } else {
                $corrections = Correction::with('attendance')
                ->where('user_id', $user->id)
                ->where('status', '承認待ち')
                ->get();
            }
        }

        return view('request', compact('user', 'corrections', 'page'));
    }
}
