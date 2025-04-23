<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class CorrectionTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('corrections')->insert([
            'attendance_id' => 1,
            'user_id' => 2,
            'new_work_start' => '09:00:00',
            'new_work_end' => '18:00:00',
            'new_breaks' => '[{"end": "13:30", "start": "12:30"}]',
            'remarks' => '電車遅延のため',
            'status' => '承認待ち',
            'requested_at' => Carbon::now(),
        ]);

        DB::table('corrections')->insert([
            'attendance_id' => 2,
            'user_id' => 2,
            'new_work_start' => '09:00:00',
            'new_work_end' => '18:00:00',
            'new_breaks' => '[{"end": "13:30", "start": "12:30"}]',
            'remarks' => '電車遅延のため',
            'status' => '承認済み',
            'requested_at' => Carbon::now(),
        ]);
    }
}
