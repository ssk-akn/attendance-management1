<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Correction;

class CorrectionController extends Controller
{
    public function getRequestList()
    {
        $isAdmin = Auth::user()->isAdmin();
        $page = request('page', 'wait');

        if ($page === 'approved') {
            $corrections = Correction::with('attendance', 'user')
            ->where('status', '承認済み')
            ->get();
        } else {
            $corrections = Correction::with('attendance', 'user')
            ->where('status', '承認待ち')
            ->get();
        }

        $user = $correction->user;

        return view('request', compact('corrections', 'page', 'user', 'isAdmin'));
    }
}
