<?php

namespace Modules\Break\Database\Seeders;

use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Modules\Break\Entities\UserBreak;
use Illuminate\Support\Facades\Schema;

class UserBreaksTableSeeder extends Seeder
{
    public function run()
    {
        Schema::disableForeignKeyConstraints();

        $breaks = [
            [
                'user_id'       => 1,
                'break_type_id' => 1,
                'date'          => Carbon::now()->subDays(2)->toDateString(),
                'start_time'    => '12:00:00',
                'end_time'      => '12:30:00',
                'duration'      => '00:30:00',
                'reason'        => 'Lunch break',
                'created_at'    => Carbon::now()->subDays(2),
                'updated_at'    => Carbon::now()->subDays(2),
                'company_id'    => 1,
                'branch_id'     => 1,
            ],
            [
                'user_id'       => 2,
                'break_type_id' => 2,
                'date'          => Carbon::now()->subDays(1)->toDateString(),
                'start_time'    => '15:00:00',
                'end_time'      => '15:15:00',
                'duration'      => '00:15:00',
                'reason'        => 'Tea break',
                'created_at'    => Carbon::now()->subDays(1),
                'updated_at'    => Carbon::now()->subDays(1),
                'company_id'    => 1,
                'branch_id'     => 2,
            ],
            // Add more break entries as needed
        ];

        // Insert data into the database
        foreach ($breaks as $break) {
            UserBreak::create($break);
        }

        Schema::enableForeignKeyConstraints();
    }
}
