<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use Carbon\Carbon;
use App\Models\User;
use App\Models\Attendance;
use App\Models\BreakTime;

class DetailTest extends TestCase
{
    use RefreshDatabase;
    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function test_user_name_is_displayed()
    {
        $dateAndTime = Carbon::create(2025, 4, 30, 12, 30);
        Carbon::setTestNow($dateAndTime);

        $user = User::factory()->create([
            'email_verified_at' => now(),
        ]);

        $attendance = Attendance::create([
            'user_id' => $user->id,
            'date' => '2025-04-01',
            'work_start' => '08:45:00',
            'work_end' => '17:45:00',
        ]);

        $response = $this->actingAs($user)->get("/attendance/{$attendance->id}");
        $response->assertStatus(200);
        $response->assertSee($user->name);

        Carbon::setTestNow();
    }

    public function test_selected_date_is_displayed()
    {
        $dateAndTime = Carbon::create(2025, 4, 30, 12, 30);
        Carbon::setTestNow($dateAndTime);

        $user = User::factory()->create([
            'email_verified_at' => now(),
        ]);

        $attendance = Attendance::create([
            'user_id' => $user->id,
            'date' => '2025-04-01',
            'work_start' => '08:45:00',
            'work_end' => '17:45:00',
        ]);

        $response = $this->actingAs($user)->get("/attendance/{$attendance->id}");
        $response->assertStatus(200);
        $response->assertSee(Carbon::parse($attendance->date)->isoFormat('YYYY年'));
        $response->assertSee(Carbon::parse($attendance->date)->isoFormat('M月D日'));

        Carbon::setTestNow();
    }

    public function test_work_times_match_user_records()
    {
        $dateAndTime = Carbon::create(2025, 4, 30, 12, 30);
        Carbon::setTestNow($dateAndTime);

        $user = User::factory()->create([
            'email_verified_at' => now(),
        ]);

        $attendance = Attendance::create([
            'user_id' => $user->id,
            'date' => '2025-04-01',
            'work_start' => '08:45:00',
            'work_end' => '17:45:00',
        ]);

        $response = $this->actingAs($user)->get("/attendance/{$attendance->id}");
        $response->assertStatus(200);
        $response->assertSee(Carbon::parse($attendance->work_start)->isoFormat('HH:mm'));
        $response->assertSee(Carbon::parse($attendance->work_end)->isoFormat('HH:mm'));

        Carbon::setTestNow();
    }

    public function test_break_times_match_user_records()
    {
        $dateAndTime = Carbon::create(2025, 4, 30, 12, 30);
        Carbon::setTestNow($dateAndTime);

        $user = User::factory()->create([
            'email_verified_at' => now(),
        ]);

        $attendance = Attendance::create([
            'user_id' => $user->id,
            'date' => '2025-04-01',
            'work_start' => '08:45:00',
            'work_end' => '17:45:00',
        ]);

        $breakTime = BreakTime::create([
            'attendance_id' => $attendance->id,
            'break_start' => '13:00:00',
            'break_end' => '14:00:00',
        ]);

        $response = $this->actingAs($user)->get("/attendance/{$attendance->id}");
        $response->assertStatus(200);
        $response->assertSee(Carbon::parse($breakTime->break_start)->isoFormat('HH:mm'));
        $response->assertSee(Carbon::parse($breakTime->break_end)->isoFormat('HH:mm'));

        Carbon::setTestNow();
    }
}
