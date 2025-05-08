<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use Carbon\Carbon;
use App\Models\User;
use App\Models\Attendance;

class ClockOutTest extends TestCase
{
    use RefreshDatabase;
    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function test_clock_out_button_works_properly()
    {
        $dateAndTime = Carbon::create(2025, 4, 1, 8, 30);
        Carbon::setTestNow($dateAndTime);

        $user = User::factory()->create();

        Attendance::create([
            'user_id' => $user->id,
            'date' => $dateAndTime->toDateString(),
            'work_start' => $dateAndTime->toTimeString(),
        ]);

        $response = $this->actingAs($user)->get('/attendance');
        $response->assertStatus(200);
        $response->assertSee('出勤中');
        $response->assertSee('退勤');

        Carbon::setTestNow($dateAndTime->addMinute(10));
        $this->patch('/attendance/work-end');

        $response = $this->get('/attendance');
        $response->assertSee('退勤済');

        Carbon::setTestNow();
    }

    public function test_clock_out_time_is_recorded_and_displayed()
    {
        $dateAndTime = Carbon::create(2025, 4, 1, 8, 30);
        Carbon::setTestNow($dateAndTime);

        $user = User::factory()->create();

        $response = $this->actingAs($user)->get('/attendance');
        $response->assertStatus(200);
        $response->assertSee('勤務外');

        $this->post('/attendance/work-start');
        Carbon::setTestNow($dateAndTime->copy()->addMinute(10));
        $this->patch('/attendance/work-end');

        $response = $this->get('/attendance/list');
        $response->assertSee('08:40');

        Carbon::setTestNow();
    }
}
