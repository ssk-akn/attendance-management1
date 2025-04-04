<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;
use App\Models\BreakTime;
use App\Models\Correction;
use Carbon\Carbon;

class Attendance extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'date',
        'work_start',
        'work_end',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function breaks()
    {
        return $this->hasMany(BreakTime::class);
    }

    public function corrections()
    {
        return $this->hasMany(Correction::class);
    }

    public function getFormattedStartAttribute()
    {
        return $this->work_start ? Carbon::parse($this->work_start)->isoFormat('HH:mm') : '';
    }

    public function getFormattedEndAttribute()
    {
        return $this->work_end ? Carbon::parse($this->work_end)->isoFormat('HH:mm') : '';
    }

    public function getFormattedYearAttribute()
    {
        return Carbon::parse($this->date)->isoFormat('YYYY年');
    }

    public function getFormattedDateAttribute()
    {
        return Carbon::parse($this->date)->isoFormat('M月D日');
    }

    public function getFormattedWeekAttribute()
    {
        return Carbon::parse($this->date)->isoFormat('(ddd)');
    }

    public function getFormattedStampAttribute()
    {
        return Carbon::parse($this->date)->isoFormat('YYYY/MM/DD');
    }
}
