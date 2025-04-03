<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Attendance;
use Carbon\Carbon;

class BreakTime extends Model
{
    use HasFactory;

    protected $table = 'breaks';

    protected $fillable = [
        'attendance_id',
        'break_start',
        'break_end',
    ];

    public function attendance()
    {
        return $this->belongsTo(Attendance::class);
    }

    public function getFormattedStartAttribute()
    {
        return $this->break_start ? Carbon::parse($this->break_start)->isoFormat('HH:mm') : '';
    }

    public function getFormattedEndAttribute()
    {
        return $this->break_end ? Carbon::parse($this->break_end)->isoFormat('HH:mm') : '';
    }
}
