<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use Carbon\Carbon;
use App\Models\User;
use App\Models\Attendance;
use App\Models\BreakTime;
use App\Models\Correction;

class CorrectionTest extends TestCase
{
    use RefreshDatabase;
    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function test_validation_fails_if_start_is_after_end()
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
        $response = $this->post('/attendance/correction', [
            'new_work_start' => '20:00',
            'new_work_end' => '14:45',
            'remarks' => '打刻漏れのため',
        ]);
        $response->assertSessionHasErrors(['new_work_start']);
        $errors = session('errors');
        $this->assertEquals(
            '出勤時間もしくは退勤時間が不適切な値です',
            $errors->first('new_work_start'
        ));

        Carbon::setTestNow();
    }

    public function test_validation_fails_if_break_start_is_after_end()
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

        BreakTime::create([
            'attendance_id' => $attendance->id,
            'break_start' => '13:00:00',
            'break_end' => '14:00:00',
        ]);

        $response = $this->actingAs($user)->get("/attendance/{$attendance->id}");
        $response->assertStatus(200);
        $response = $this->post('/attendance/correction', [
            'new_work_start' => '8:45',
            'new_work_end' => '17:45',
            'new_break_start' => ['18:00'],
            'new_break_end' => ['14:00'],
            'remarks' => '打刻漏れのため',
        ]);

        $response->assertSessionHasErrors(['new_break_start.0']);

        $errors = session('errors');
        $this->assertEquals(
            '休憩時間が勤務時間外です',
            $errors->first('new_break_start.0')
        );

        Carbon::setTestNow();
    }

    public function test_validation_fails_if_break_end_is_after_end()
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

        BreakTime::create([
            'attendance_id' => $attendance->id,
            'break_start' => '13:00:00',
            'break_end' => '14:00:00',
        ]);

        $response = $this->actingAs($user)->get("/attendance/{$attendance->id}");
        $response->assertStatus(200);
        $response = $this->post('/attendance/correction', [
            'new_work_start' => '8:45',
            'new_work_end' => '17:45',
            'new_break_start' => ['13:00'],
            'new_break_end' => ['18:00'],
            'remarks' => '打刻漏れのため',
        ]);

        $response->assertSessionHasErrors(['new_break_start.0']);

        $errors = session('errors');
        $this->assertEquals(
            '休憩時間が勤務時間外です',
            $errors->first('new_break_start.0')
        );

        Carbon::setTestNow();
    }

    public function test_remarks_is_required()
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
        $response = $this->post('/attendance/correction', [
            'new_work_start' => '8:45',
            'new_work_end' => '17:45',
            // 'remarks' => '打刻漏れのため',
        ]);

        $response->assertSessionHasErrors(['remarks']);
        $errors = session('errors');
        $this->assertEquals('備考を記入してください', $errors->first('remarks'));

        Carbon::setTestNow();
    }

    public function test_correction_application_process_is_executed()
    {
        $dateAndTime = Carbon::create(2025, 4, 30, 12, 30);
        Carbon::setTestNow($dateAndTime);

        $user = User::factory()->create([
            'email_verified_at' => now(),
        ]);

        $admin = User::factory()->create([
            'role' =>'admin',
        ]);

        $attendance = Attendance::create([
            'user_id' => $user->id,
            'date' => '2025-04-01',
            'work_start' => '08:45:00',
            'work_end' => '17:45:00',
        ]);

        $this->actingAs($user);
        $this->post('/attendance/correction', [
            'user_id' => $user->id,
            'attendance_id' => $attendance->id,
            'new_work_start' => '09:00',
            'new_work_end' => '18:00',
            'remarks' => '電車遅延のため',
        ]);

        $correction = Correction::where('attendance_id', $attendance->id)->first();

        $response = $this->actingAs($admin)->get("/stamp_correction_request/approve/{$correction->id}");
        $response->assertStatus(200);
        $response->assertSee('9:00');
        $response->assertSee('18:00');
        $response->assertSee('電車遅延のため');

        $response = $this->get('/stamp_correction_request/list');
        $response->assertStatus(200);
        $response->assertSee('承認待ち');
        $response->assertSee($user->name);
        $response->assertSee('電車遅延のため');

        Carbon::setTestNow();
    }

    public function test_application_is_displayed()
    {
        $dateAndTime = Carbon::create(2025, 4, 30, 12, 30);
        Carbon::setTestNow($dateAndTime);

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

        $this->actingAs($user);
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

    public function test_approved_correction_application_is_displayed()
    {
        $dateAndTime = Carbon::create(2025, 4, 30, 12, 30);
        Carbon::setTestNow($dateAndTime);

        $user = User::factory()->create([
            'email_verified_at' => now(),
        ]);
        $admin = User::factory()->create([
            'role' =>'admin',
        ]);

        $attendance = Attendance::create([
            'user_id' => $user->id,
            'date' => '2025-04-01',
            'work_start' => '08:45:00',
            'work_end' => '17:45:00',
        ]);

        $this->actingAs($user);
        $this->post('/attendance/correction', [
            'user_id' => $user->id,
            'attendance_id' => $attendance->id,
            'new_work_start' => '09:00',
            'new_work_end' => '18:00',
            'remarks' => '電車遅延のため',
        ]);

        $correction = Correction::where('attendance_id', $attendance->id)->first();

        $this->actingAs($admin)->put('/stamp_correction_request/approve/update', [
            'correction_id' => $correction->id,
            'attendance_id' => $attendance->id,
        ]);

        $response = $this->get('/stamp_correction_request/list/?page=approved');
        $response->assertStatus(200);
        $response->assertSee('承認済み');
        $response->assertSee($user->name);
        $response->assertSee('2025/04/01');
        $response->assertSee('電車遅延のため');

        Carbon::setTestNow();
    }

    public function test_user_can_view_correction_request_detail_page()
    {
        $dateAndTime = Carbon::create(2025, 4, 30, 12, 30);
        Carbon::setTestNow($dateAndTime);

        $user = User::factory()->create([
            'email_verified_at' => now(),
        ]);
        $admin = User::factory()->create([
            'role' =>'admin',
        ]);

        $attendance = Attendance::create([
            'user_id' => $user->id,
            'date' => '2025-04-01',
            'work_start' => '08:45:00',
            'work_end' => '17:45:00',
        ]);

        $this->actingAs($user);
        $this->post('/attendance/correction', [
            'user_id' => $user->id,
            'attendance_id' => $attendance->id,
            'new_work_start' => '09:00',
            'new_work_end' => '18:00',
            'remarks' => '電車遅延のため',
        ]);

        $correction = Correction::where('attendance_id', $attendance->id)->first();

        $response = $this->get('/stamp_correction_request/list');
        $response->assertStatus(200);
        $response = $this->actingAs($admin)->get("/stamp_correction_request/approve/{$correction->id}");
        $response->assertStatus(200);

        Carbon::setTestNow();
    }
}
