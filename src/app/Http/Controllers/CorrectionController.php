<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Correction;

class CorrectionController extends Controller
{
    public function getRequestList()
    {
        $userId = Auth::id();

        $corrections = Correction::where('user_id', $userId)->all();

        return view('request', compact('corrections'));
    }
}
