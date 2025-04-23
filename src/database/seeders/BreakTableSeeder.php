<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class BreakTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('breaks')->insert([
            'attendance_id' => 1,
            'break_start' => '12:00:00',
            'break_end' => '13:00:00',
        ]);
        DB::table('breaks')->insert([
            'attendance_id' => 2,
            'break_start' => '12:30:00',
            'break_end' => '13:30:00',
        ]);
        DB::table('breaks')->insert([
            'attendance_id' => 3,
            'break_start' => '12:00:00',
            'break_end' => '13:00:00',
        ]);
        DB::table('breaks')->insert([
            'attendance_id' => 4,
            'break_start' => '12:00:00',
            'break_end' => '13:00:00',
        ]);
        DB::table('breaks')->insert([
            'attendance_id' => 5,
            'break_start' => '12:00:00',
            'break_end' => '13:00:00',
        ]);
        DB::table('breaks')->insert([
            'attendance_id' => 6,
            'break_start' => '12:00:00',
            'break_end' => '13:00:00',
        ]);
        DB::table('breaks')->insert([
            'attendance_id' => 7,
            'break_start' => '12:00:00',
            'break_end' => '13:00:00',
        ]);
    }
}
