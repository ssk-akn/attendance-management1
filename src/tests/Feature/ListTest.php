<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use Carbon\Carbon;
use App\Models\User;
use App\Models\Attendance;
use App\Models\BreakTime;

class ListTest extends TestCase
{
    use RefreshDatabase;
    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function test_user_attendance_list_display()
    {
        $dateAndTime = Carbon::create(2025, 4, 30, 12, 30);
        Carbon::setTestNow($dateAndTime);

        $user = User::factory()->create();

        $attendance1 = Attendance::create([
            'user_id' => $user->id,
            'date' => '2025-04-01',
            'work_start' => '08:45:00',
            'work_end' => '17:45:00',
        ]);
        $attendance2 = Attendance::create([
            'user_id' => $user->id,
            'date' => '2025-04-02',
            'work_start' => '07:00:00',
            'work_end' => '16:00:00',
        ]);

        BreakTime::create([
            'attendance_id' => $attendance1->id,
            'break_start' => '13:00:00',
            'break_end' => '14:00:00',
        ]);
        BreakTime::create([
            'attendance_id' => $attendance2->id,
            'break_start' => '12:00:00',
            'break_end' => '13:00:00',
        ]);

        $response = $this->actingAs($user)->get('/attendance/list');
        $response->assertStatus(200);
        $response->assertSee('08:45');
        $response->assertSee('17:45');
        $response->assertSee('07:00');
        $response->assertSee('16:00');
        $response->assertSee('1:00');
        $response->assertSee('8:00');

        Carbon::setTestNow();
    }

    public function test_this_month_list_display()
    {
        $now = Carbon::now();
        Carbon::setTestNow($now);

        $user = User::factory()->create();

        $response = $this->actingAs($user)->get('/attendance/list');
        $response->assertStatus(200);
        $response->assertSee($now->isoFormat('YYYY/MM'));

        Carbon::setTestNow();
    }

    public function test_previous_month_display()
    {
        $dateAndTime = Carbon::create(2025, 4, 1, 8, 30);
        Carbon::setTestNow($dateAndTime);

        $user = User::factory()->create();

        $attendance1 = Attendance::create([
            'user_id' => $user->id,
            'date' => '2025-03-01',
            'work_start' => '08:45:00',
            'work_end' => '17:45:00',
        ]);
        $attendance2 = Attendance::create([
            'user_id' => $user->id,
            'date' => '2025-03-02',
            'work_start' => '07:00:00',
            'work_end' => '16:00:00',
        ]);

        BreakTime::create([
            'attendance_id' => $attendance1->id,
            'break_start' => '13:00:00',
            'break_end' => '14:00:00',
        ]);
        BreakTime::create([
            'attendance_id' => $attendance2->id,
            'break_start' => '12:00:00',
            'break_end' => '13:00:00',
        ]);

        $response = $this->actingAs($user)->get('/attendance/list');
        $response->assertStatus(200);

        $year = $dateAndTime->copy()->subMonth(1)->isoFormat('YYYY');
        $month = $dateAndTime->copy()->subMonth(1)->isoFormat('MM');
        $response = $this->get("/attendance/list/?year=$year&month=$month");
        $response->assertSee('08:45');
        $response->assertSee('17:45');
        $response->assertSee('07:00');
        $response->assertSee('16:00');
        $response->assertSee('1:00');
        $response->assertSee('8:00');

        Carbon::setTestNow();
    }

    public function test_next_month_display()
    {
        $dateAndTime = Carbon::create(2025, 3, 1, 8, 30);
        Carbon::setTestNow($dateAndTime);

        $user = User::factory()->create();

        $attendance1 = Attendance::create([
            'user_id' => $user->id,
            'date' => '2025-04-01',
            'work_start' => '08:45:00',
            'work_end' => '17:45:00',
        ]);
        $attendance2 = Attendance::create([
            'user_id' => $user->id,
            'date' => '2025-04-02',
            'work_start' => '07:00:00',
            'work_end' => '16:00:00',
        ]);

        BreakTime::create([
            'attendance_id' => $attendance1->id,
            'break_start' => '13:00:00',
            'break_end' => '14:00:00',
        ]);
        BreakTime::create([
            'attendance_id' => $attendance2->id,
            'break_start' => '12:00:00',
            'break_end' => '13:00:00',
        ]);

        $response = $this->actingAs($user)->get('/attendance/list');
        $response->assertStatus(200);

        $year = $dateAndTime->copy()->addMonth(1)->isoFormat('YYYY');
        $month = $dateAndTime->copy()->addMonth(1)->isoFormat('MM');
        $response = $this->get("/attendance/list/?year=$year&month=$month");
        $response->assertSee('08:45');
        $response->assertSee('17:45');
        $response->assertSee('07:00');
        $response->assertSee('16:00');
        $response->assertSee('1:00');
        $response->assertSee('8:00');

        Carbon::setTestNow();
    }

    public function test_detail_button_redirects_to_detail_page()
    {
        $dateAndTime = Carbon::create(2025, 4, 30, 12, 30);
        Carbon::setTestNow($dateAndTime);

        $user = User::factory()->create();

        $attendance = Attendance::create([
            'user_id' => $user->id,
            'date' => '2025-04-01',
            'work_start' => '08:45:00',
            'work_end' => '17:45:00',
        ]);

        BreakTime::create([
            'attendance_id' => $attendance->id,
            'break_start' => '13:00:00',
            'break_end' => '14:00:00',
        ]);

        $response = $this->actingAs($user)->get('/attendance/list');
        $response->assertStatus(200);

        $response = $this->get("attendance/{$attendance->id}");
        $response->assertStatus(200);

        Carbon::setTestNow();
    }
}
