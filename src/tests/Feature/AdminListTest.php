<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use Carbon\Carbon;
use App\Models\User;
use App\Models\Attendance;

class AdminListTest extends TestCase
{
    use RefreshDatabase;
    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function test_admin_can_view_all_users_attendance_for_the_day()
    {
        $dateAndTime = Carbon::create(2025, 4, 1, 21, 30);
        Carbon::setTestNow($dateAndTime);

        $admin = User::factory()->create([
            'role' =>'admin',
        ]);
        $user1 = User::factory()->create([
            'email_verified_at' => now(),
        ]);
        $user2 = User::factory()->create([
            'email_verified_at' => now(),
        ]);

        $attendance1 = Attendance::create([
            'user_id' => $user1->id,
            'date' => '2025-04-01',
            'work_start' => '08:45:00',
            'work_end' => '17:45:00',
        ]);
        $attendance2 = Attendance::create([
            'user_id' => $user2->id,
            'date' => '2025-04-01',
            'work_start' => '07:00:00',
            'work_end' => '16:00:00',
        ]);

        $response = $this->actingAs($admin)->get('/admin/attendance/list');
        $response->assertStatus(200);
        $response->assertSee($user1->name);
        $response->assertSee('08:45');
        $response->assertSee($user2->name);
        $response->assertSee('07:00');

        Carbon::setTestNow();
    }

    public function test_current_date_is_displayed()
    {
        $dateAndTime = Carbon::create(2025, 4, 1, 21, 30);
        Carbon::setTestNow($dateAndTime);

        $admin = User::factory()->create([
            'role' =>'admin',
        ]);

        $response = $this->actingAs($admin)->get('/admin/attendance/list');
        $response->assertStatus(200);
        $response->assertSee('2025年4月1日');

        Carbon::setTestNow();
    }

    public function test_admin_can_view_previous_day_attendance()
    {
        $dateAndTime = Carbon::create(2025, 4, 2, 21, 30);
        Carbon::setTestNow($dateAndTime);

        $admin = User::factory()->create([
            'role' =>'admin',
        ]);
        $user = User::factory()->create([
            'email_verified_at' => now(),
        ]);

        $attendance1 = Attendance::create([
            'user_id' => $user->id,
            'date' => '2025-04-01',
            'work_start' => '08:45:00',
            'work_end' => '17:45:00',
        ]);

        $response = $this->actingAs($admin)->get('/admin/attendance/list');
        $response->assertStatus(200);
        $response = $this->get(route('admin.list', [
            'year' => $dateAndTime->copy()->subDay()->year,
            'month' => $dateAndTime->copy()->subDay()->month,
            'day' => $dateAndTime->copy()->subDay()->day,
        ]));
        $response->assertSee('2025年4月1日');
        $response->assertSee($user->name);
        $response->assertSee('08:45');

        Carbon::setTestNow();
    }

    public function test_admin_can_view_next_day_attendance()
    {
        $dateAndTime = Carbon::create(2025, 4, 2, 21, 30);
        Carbon::setTestNow($dateAndTime);

        $admin = User::factory()->create([
            'role' =>'admin',
        ]);
        $user = User::factory()->create([
            'email_verified_at' => now(),
        ]);

        $attendance = Attendance::create([
            'user_id' => $user->id,
            'date' => '2025-04-03',
            'work_start' => '08:45:00',
            'work_end' => '17:45:00',
        ]);

        $response = $this->actingAs($admin)->get('/admin/attendance/list');
        $response->assertStatus(200);
        $response = $this->get(route('admin.list', [
            'year' => $dateAndTime->copy()->addDay()->year,
            'month' => $dateAndTime->copy()->addDay()->month,
            'day' => $dateAndTime->copy()->addDay()->day,
        ]));
        $response->assertSee('2025年4月3日');
        $response->assertSee($user->name);
        $response->assertSee('08:45');

        Carbon::setTestNow();
    }
}
