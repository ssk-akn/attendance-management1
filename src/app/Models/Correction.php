<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;
use App\Models\Attendance;
use Carbon\Carbon;

class Correction extends Model
{
    use HasFactory;
    
    public $timestamps = false;

    protected $fillable = [
        'attendance_id',
        'user_id',
        'remarks',
        'status',
        'requested_at',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function attendance()
    {
        return $this->belongsTo(Attendance::class);
    }

    public function getFormattedStartAttribute()
    {
        return $this->new_work_start ? Carbon::parse($this->new_work_start)->isoFormat('HH:mm') : '';
    }

    public function getFormattedEndAttribute()
    {
        return $this->new_work_end ? Carbon::parse($this->new_work_end)->isoFormat('HH:mm') : '';
    }

    protected $casts = [
        'new_breaks' => 'array',
    ];
}
