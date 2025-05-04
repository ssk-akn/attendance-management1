<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use Carbon\Carbon;
use App\Models\User;
use App\Models\Attendance;
use App\Models\BreakTime;

class AdminDetailTest extends TestCase
{
    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function test_attendance_detail_displays_selected_record()
    {
        $dateAndTime = Carbon::create(2025, 4, 1, 21, 30);
        Carbon::setTestNow($dateAndTime);

        $admin = User::factory()->create([
            'role' => 'admin',
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

        $response = $this->actingAs($admin)->get("/attendance/{$attendance->id}");
        $response->assertStatus(200);
        $response->assertSee($user->name);
        $response->assertSee('2025-04-01');
        $response->assertSee('08:45');
        $response->assertSee('17:45');
    }

    public function test_validation_fails_if_start_is_after_end()
    {
        $dateAndTime = Carbon::create(2025, 4, 1, 21, 30);
        Carbon::setTestNow($dateAndTime);

        $admin = User::factory()->create([
            'role' => 'admin',
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

        $response = $this->actingAs($admin)->get("/attendance/{$attendance->id}");
        $response->assertStatus(200);
        $response = $this->put('/attendance/correction/update', [
            'new_work_start' => '20:00',
            'new_work_end' => '14:45',
            'remarks' => '打刻漏れのため',
        ]);
        $response->assertSessionHasErrors(['new_work_start']);
        $errors = session('errors');
        $this->assertEquals(
            '出勤時間もしくは退勤時間が不適切な値です',
            $errors->first('new_work_start')
        );

        Carbon::setTestNow();
    }

    public function test_validation_fails_if_break_start_is_after_end()
    {
        $dateAndTime = Carbon::create(2025, 4, 1, 21, 30);
        Carbon::setTestNow($dateAndTime);

        $admin = User::factory()->create([
            'role' => 'admin',
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

        BreakTime::create([
            'attendance_id' => $attendance->id,
            'break_start' => '13:00:00',
            'break_end' => '14:00:00',
        ]);

        $response = $this->actingAs($admin)->get("/attendance/{$attendance->id}");
        $response->assertStatus(200);
        $response = $this->put('/attendance/correction/update', [
            'new_work_start' => '8:45',
            'new_work_end' => '17:45',
            'new_break_start' => ['18:00'],
            'new_break_end' => ['14:00'],
            'remarks' => '打刻漏れのため',
        ]);

        $response->assertSessionHasErrors(['new_break_start.0' => '休憩時間が勤務時間外です']);
        $errors = session('errors');
        $this->assertEquals(
            '休憩時間が勤務時間外です',
            $errors->first('new_break_start.0')
        );

        Carbon::setTestNow();
    }

    public function test_validation_fails_if_break_end_is_after_end()
    {
        $dateAndTime = Carbon::create(2025, 4, 1, 21, 30);
        Carbon::setTestNow($dateAndTime);

        $admin = User::factory()->create([
            'role' => 'admin',
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

        BreakTime::create([
            'attendance_id' => $attendance->id,
            'break_start' => '13:00:00',
            'break_end' => '14:00:00',
        ]);

        $response = $this->actingAs($admin)->get("/attendance/{$attendance->id}");
        $response->assertStatus(200);
        $response = $this->put('/attendance/correction/update', [
            'new_work_start' => '8:45',
            'new_work_end' => '17:45',
            'new_break_start' => ['13:00'],
            'new_break_end' => ['18:00'],
            'remarks' => '打刻漏れのため',
        ]);

        $response->assertSessionHasErrors(['new_break_start.0' => '休憩時間が勤務時間外です']);
        $errors = session('errors');
        $this->assertEquals(
            '休憩時間が勤務時間外です',
            $errors->first('new_break_start.0')
        );

        Carbon::setTestNow();
    }

    public function test_remarks_is_required()
    {
        $dateAndTime = Carbon::create(2025, 4, 1, 21, 30);
        Carbon::setTestNow($dateAndTime);

        $admin = User::factory()->create([
            'role' => 'admin',
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

        $response = $this->actingAs($admin)->get("/attendance/{$attendance->id}");
        $response->assertStatus(200);
        $response = $this->put('/attendance/correction/update', [
            'new_work_start' => '8:45',
            'new_work_end' => '17:45',
            // 'remarks' => '打刻漏れのため',
        ]);

        $response->assertSessionHasErrors(['remarks']);
        $errors = session('errors');
        $this->assertEquals('備考を記入してください', $errors->first('remarks'));

        Carbon::setTestNow();
    }
}
