<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use Carbon\Carbon;
use App\Models\User;
use App\Models\Attendance;

class ClockInTest extends TestCase
{
    use RefreshDatabase;
    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function test_clock_in_button_works_properly()
    {
        $user = User::factory()->create([
            'email_verified_at' => now(),
        ]);

        $response = $this->actingAs($user)->get('/attendance');
        $response->assertStatus(200);
        $response->assertSee('出勤');

        $response = $this->post('/attendance/work-start');

        $response = $this->actingAs($user)->get('/attendance');
        $response->assertStatus(200);
        $response->assertSee('出勤中');
    }

    public function test_go_to_work_once_a_day()
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
        $response->assertDontSee('出勤');

        Carbon::setTestNow();
    }

    public function test_clock_in_time_is_recorded_and_displayed()
    {
        $now = Carbon::now();
        Carbon::setTestNow($now);

        $user = User::factory()->create([
            'email_verified_at' => now(),
        ]);

        $response = $this->actingAs($user)->get('/attendance');
        $response->assertStatus(200);
        $response->assertSee('勤務外');

        $response = $this->post('/attendance/work-start');

        $response = $this->actingAs($user)->get('/attendance/list');
        $response->assertStatus(200);
        $response->assertSee($now->isoFormat('MM/DD(ddd)'));
        $response->assertSee($now->isoFormat('HH:mm'));

        Carbon::setTestNow();
    }
}
