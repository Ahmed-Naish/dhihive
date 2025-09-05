<?php

namespace Modules\Notify\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;

class NotificationReceiptSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('jm_notification_receipts')->insert([
            [
                'notification_id' => 1,
                'user_id' => 1,
                'seen' => true,
                'seen_at' => now(),
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'notification_id' => 2,
                'user_id' => 1,
                'seen' => false,
                'seen_at' => null,
                'created_at' => now(),
                'updated_at' => now()
            ]
        ]);
    }
}
