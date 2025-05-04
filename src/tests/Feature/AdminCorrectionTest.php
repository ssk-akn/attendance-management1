<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use Carbon\Carbon;
use App\Models\User;
use App\Models\Attendance;
use App\Models\Correction;

class AdminCorrectionTest extends TestCase
{
    use RefreshDatabase;
    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function test_application_is_displayed()
    {
        $dateAndTime = Carbon::create(2025, 4, 30, 12, 30);
        Carbon::setTestNow($dateAndTime);

        $admin = User::factory()->create([
            'role' => 'admin',
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
            'date' => '2025-04-02',
            'work_start' => '08:45:00',
            'work_end' => '17:45:00',
        ]);
        $attendance3 = Attendance::create([
            'user_id' => $user->id,
            'date' => '2025-04-03',
            'work_start' => '08:45:00',
            'work_end' => '17:45:00',
        ]);

        $correction1 = Correction::create([
            'attendance_id' => $attendance1->id,
            'user_id' => $user->id,
            'new_work_start' => '09:00:00',
            'new_work_end' => '18:00:00',
            'remarks' => '電車遅延のため',
            'status' => '認証待ち',
        ]);
        $correction2 = Correction::create([
            'attendance_id' => $attendance2->id,
            'user_id' => $user->id,
            'new_work_start' => '07:00:00',
            'new_work_end' => '16:00:00',
            'remarks' => '打刻漏れのため',
            'status' => '認証待ち',
        ]);
        $correction3 = Correction::create([
            'attendance_id' => $attendance3->id,
            'user_id' => $user->id,
            'new_work_start' => '10:00:00',
            'new_work_end' => '19:00:00',
            'remarks' => '打刻漏れのため',
            'status' => '認証済み',
        ]);

        $this->actingAs($admin)->get('/stamp_correction_request/list');
        $this->post('/attendance/correction', [
            'user_id' => $user->id,
            'attendance_id' => $attendance1->id,
            'new_work_start' => '09:00',
            'new_work_end' => '18:00',
            'remarks' => '電車遅延のため',
        ]);
        $this->post('/attendance/correction', [
            'user_id' => $user->id,
            'attendance_id' => $attendance2->id,
            'new_work_start' => '07:00',
            'new_work_end' => '16:00',
            'remarks' => '打刻漏れのため',
        ]);

        $response = $this->get('/stamp_correction_request/list');
        $response->assertStatus(200);
        $response->assertSee('承認待ち');
        $response->assertSee($user->name);
        $response->assertSee('2025/04/01');
        $response->assertSee('電車遅延のため');
        $response->assertSee('2025/04/02');
        $response->assertSee('打刻漏れのため');

        Carbon::setTestNow();
    }
}
