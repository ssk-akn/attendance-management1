<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use Carbon\Carbon;
use App\Models\User;
use App\Models\Attendance;

class BreakTest extends TestCase
{
    use RefreshDatabase;
    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function test_break_button_works_properly()
    {
        $now = Carbon::now();
        Carbon::setTestNow($now);

        $user = User::factory()->create();

        Attendance::create([
            'user_id' => $user->id,
            'date' => $now->toDateString(),
            'work_start' => $now->toTimeString(),
        ]);

        $response = $this->actingAs($user)->get('/attendance');
        $response->assertStatus(200);
        $response->assertSee('出勤中');
        $response->assertSee('休憩入');

        $this->post('/attendance/break-start');

        $response = $this->get('/attendance');
        $response->assertSee('休憩中');

        Carbon::setTestNow();
    }

    public function test_user_can_take_multiple_break()
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

        $this->post('/attendance/break-start');
        Carbon::setTestNow($dateAndTime->copy()->addMinutes(10));
        $this->patch('/attendance/break-end');

        $response = $this->get('/attendance');
        $response->assertSee('休憩入');

        Carbon::setTestNow();
    }

    public function test_break_return_button_works_properly()
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

        $this->post('/attendance/break-start');

        $response = $this->get('/attendance');
        $response->assertSee('休憩戻');

        Carbon::setTestNow($dateAndTime->copy()->addMinutes(10));
        $this->patch('/attendance/break-end');

        $response = $this->get('/attendance');
        $response->assertSee('出勤中');

        Carbon::setTestNow();
    }

    public function test_user_can_push_multiple_break_return()
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

        $this->post('/attendance/break-start');
        Carbon::setTestNow($dateAndTime->copy()->addMinutes(10));
        $this->patch('/attendance/break-end');
        Carbon::setTestNow($dateAndTime->copy()->addMinutes(20));
        $this->post('/attendance/break-start');

        $response = $this->get('/attendance');
        $response->assertSee('休憩戻');

        Carbon::setTestNow();
    }

    public function test__break_time_is_recorded_and_displayed()
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

        $this->post('/attendance/break-start');
        Carbon::setTestNow($dateAndTime->copy()->addMinutes(10));
        $this->patch('/attendance/break-end');

        $response = $this->get('/attendance/list');
        $response->assertSee('00:10');

        Carbon::setTestNow();
    }
}
