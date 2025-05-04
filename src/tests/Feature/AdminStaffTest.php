<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use Carbon\Carbon;
use App\Models\User;
use App\Models\Attendance;

class AdminStaffTest extends TestCase
{
    use RefreshDatabase;
    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function test_admin_can_view_all_general_users_name_and_email()
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

        $response = $this->actingAs($admin)->get('/admin/staff/list');
        $response->assertStatus(200);
        $response->assertSee($user1->name);
        $response->assertSee($user1->email);
        $response->assertSee($user2->name);
        $response->assertSee($user1->email);

        Carbon::setTestNow();
    }

    public function test_user_attendance_information_is_displayed_correctly()
    {
        $dateAndTime = Carbon::create(2025, 4, 1, 21, 30);
        Carbon::setTestNow($dateAndTime);

        $admin = User::factory()->create([
            'role' =>'admin',
        ]);
        $user = User::factory()->create([
            'email_verified_at' => now(),
        ]);

        $attendance = Attendance::create([
            'user_id' => $user->id,
            'date' => '2025-04-01',
            'work_start' => '08:45:00',
            'work_end' => '17:45:00',
        ]);

        $response = $this->actingAs($admin)->get("/admin/attendance/staff/{$user->id}");
        $response->assertStatus(200);
        $response->assertSee($user->name);
        $response->assertSee('08:45');
        $response->assertSee('17:45');

        Carbon::setTestNow();
    }

    public function test_previous_month_button_displays_data_for_previous_month()
    {
        $dateAndTime = Carbon::create(2025, 4, 1, 21, 30);
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
        $attendance2 = Attendance::create([
            'user_id' => $user->id,
            'date' => '2025-03-01',
            'work_start' => '07:00:00',
            'work_end' => '16:00:00',
        ]);

        $response = $this->actingAs($admin)->get("/admin/attendance/staff/{$user->id}");
        $response->assertStatus(200);
        $response = $this->get(route('admin.attendance', [
            'id' => $user->id,
            'year' => $dateAndTime->copy()->subMonth()->year,
            'month' => $dateAndTime->copy()->subMonth()->month,
        ]));
        $response->assertSee('07:00');
        $response->assertSee('16:00');
        $response->assertDontSee('08:45');

        Carbon::setTestNow();
    }

    public function test_next_month_button_displays_data_for_previous_month()
    {
        $dateAndTime = Carbon::create(2025, 4, 1, 21, 30);
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
        $attendance2 = Attendance::create([
            'user_id' => $user->id,
            'date' => '2025-05-01',
            'work_start' => '07:00:00',
            'work_end' => '16:00:00',
        ]);

        $response = $this->actingAs($admin)->get("/admin/attendance/staff/{$user->id}");
        $response->assertStatus(200);
        $response = $this->get(route('admin.attendance', [
            'id' => $user->id,
            'year' => $dateAndTime->copy()->addMonth()->year,
            'month' => $dateAndTime->copy()->addMonth()->month,
        ]));
        $response->assertSee('07:00');
        $response->assertSee('16:00');
        $response->assertDontSee('08:45');

        Carbon::setTestNow();
    }

    public function test_clicking_detail_link_navigates_to_attendance_detail_page()
    {
        $dateAndTime = Carbon::create(2025, 4, 1, 21, 30);
        Carbon::setTestNow($dateAndTime);

        $admin = User::factory()->create([
            'role' =>'admin',
        ]);
        $user = User::factory()->create([
            'email_verified_at' => now(),
        ]);

        $attendance = Attendance::create([
            'user_id' => $user->id,
            'date' => '2025-04-01',
            'work_start' => '08:45:00',
            'work_end' => '17:45:00',
        ]);

        $response = $this->actingAs($admin)->get("/admin/attendance/staff/{$user->id}");
        $response->assertStatus(200);
        $response = $this->get("/attendance/{$attendance->id}");
        $response->assertSee('2025-04-01');
        $response->assertSee('08:45');
        $response->assertSee('17:45');

        Carbon::setTestNow();
    }
}
