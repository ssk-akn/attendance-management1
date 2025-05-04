<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use Carbon\Carbon;
use App\Models\User;

class DateTest extends TestCase
{
    use RefreshDatabase;
    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function test_get_date_and_time()
    {
        $now = Carbon::now();
        Carbon::setTestNow($now);

        $user = User::factory()->create([
            'email_verified_at' => $now,
        ]);

        $response = $this->actingAs($user)->get('/attendance');

        $response->assertStatus(200);
        $response->assertSee($now->isoFormat('YYYY年M月D日(ddd)'));
        $response->assertSee($now->isoFormat('HH:mm'));

        Carbon::setTestNow();
    }
}
