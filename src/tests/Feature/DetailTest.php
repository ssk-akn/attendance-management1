<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
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
        $user = User::factory()->create();

        $attendance = Attendance::create([
            'user_id' => $user->id,
            'date' => '2025-04-01',
            'work_start' => '08:45:00',
            'work_end' => '17:45:00',
        ]);

        $response = $this->actingAs($user)->get("/attendance/{$attendance->id}");
        $response->assertStatus(200);
        $response->assertSee($user->name);
    }

    public function test_selected_date_is_displayed()
    {
        $user = User::factory()->create();

        $attendance = Attendance::create([
            'user_id' => $user->id,
            'date' => '2025-04-01',
            'work_start' => '08:45:00',
            'work_end' => '17:45:00',
        ]);

        $response = $this->actingAs($user)->get("/attendance/{$attendance->id}");
        $response->assertStatus(200);
        $response->assertSee('2025年');
        $response->assertSee('4月1日');
    }

    public function test_work_times_match_user_records()
    {
        $user = User::factory()->create();

        $attendance = Attendance::create([
            'user_id' => $user->id,
            'date' => '2025-04-01',
            'work_start' => '08:45:00',
            'work_end' => '17:45:00',
        ]);

        $response = $this->actingAs($user)->get("/attendance/{$attendance->id}");
        $response->assertStatus(200);
        $response->assertSee('08:45');
        $response->assertSee('17:45');
    }

    public function test_break_times_match_user_records()
    {
        $user = User::factory()->create();

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
        $response->assertSee('13:00');
        $response->assertSee('14:00');
    }
}
