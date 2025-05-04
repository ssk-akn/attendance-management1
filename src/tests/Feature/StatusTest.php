<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use Carbon\Carbon;
use App\Models\User;
use App\Models\Attendance;
use App\Models\BreakTime;

class StatusTest extends TestCase
{
    use RefreshDatabase;
    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function test_status_is_off_duty()
    {
        $user = User::factory()->create([
            'email_verified_at' => now(),
        ]);

        $response = $this->actingAs($user)->get('/attendance');

        $response->assertStatus(200);
        $response->assertSee('勤務外');
    }

    public function test_status_is_at_work()
    {
        $now = Carbon::now();
        Carbon::setTestNow($now);

        $user = User::factory()->create([
            'email_verified_at' => $now,
        ]);

        Attendance::create([
            'user_id' => $user->id,
            'date' => $now->toDateString(),
            'work_start' => $now->toTimeString(),
        ]);

        $response = $this->actingAs($user)->get('/attendance');

        $response->assertStatus(200);
        $response->assertSee('出勤中');

        Carbon::setTestNow();
    }

    public function test_status_taking_a_break()
    {
        $now = Carbon::now();
        Carbon::setTestNow($now);

        $user = User::factory()->create([
            'email_verified_at' => $now,
        ]);

        $attendance = Attendance::create([
            'user_id' => $user->id,
            'date' => $now->toDateString(),
            'work_start' => $now->toTimeString(),
        ]);

        BreakTime::create([
            'attendance_id' => $attendance->id,
            'user_id' => $user->id,
            'break_start' => $now->toTimeString(),
        ]);

        $response = $this->actingAs($user)->get('/attendance');

        $response->assertStatus(200);
        $response->assertSee('休憩中');

        Carbon::setTestNow();
    }

    public function test_status_is_finished_work()
    {
        $now = Carbon::now();
        Carbon::setTestNow($now);

        $user = User::factory()->create([
            'email_verified_at' => $now,
        ]);

        Attendance::create([
            'user_id' => $user->id,
            'date' => $now->toDateString(),
            'work_start' => $now->toTimeString(),
            'work_end' => $now->toTimeString(),
        ]);

        $response = $this->actingAs($user)->get('/attendance');

        $response->assertStatus(200);
        $response->assertSee('退勤済');

        Carbon::setTestNow();
    }
}
