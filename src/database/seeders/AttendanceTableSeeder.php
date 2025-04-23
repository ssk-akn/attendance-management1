<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class AttendanceTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('attendances')->insert([
            'user_id' => 2,
            'date' => Carbon::now()->subDay(7)->toDateString(),
            'work_start' => '08:30:00',
            'work_end' => '17:30:00',
        ]);
        DB::table('attendances')->insert([
            'user_id' => 2,
            'date' => Carbon::now()->subDay(6)->toDateString(),
            'work_start' => '09:00:00',
            'work_end' => '18:00:00',
        ]);
        DB::table('attendances')->insert([
            'user_id' => 2,
            'date' => Carbon::now()->subDay(5)->toDateString(),
            'work_start' => '08:30:00',
            'work_end' => '17:30:00',
        ]);
        DB::table('attendances')->insert([
            'user_id' => 2,
            'date' => Carbon::now()->subDay(4)->toDateString(),
            'work_start' => '08:30:00',
            'work_end' => '17:30:00',
        ]);
        DB::table('attendances')->insert([
            'user_id' => 2,
            'date' => Carbon::now()->subDay(3)->toDateString(),
            'work_start' => '08:30:00',
            'work_end' => '17:30:00',
        ]);
        DB::table('attendances')->insert([
            'user_id' => 2,
            'date' => Carbon::now()->subDay(2)->toDateString(),
            'work_start' => '08:30:00',
            'work_end' => '17:30:00',
        ]);
        DB::table('attendances')->insert([
            'user_id' => 2,
            'date' => Carbon::now()->subDay(1)->toDateString(),
            'work_start' => '08:30:00',
            'work_end' => '17:30:00',
        ]);
    }
}
