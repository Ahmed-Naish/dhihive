<?php

namespace Modules\Notify\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;

class NotificationLogSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('jm_notification_logs')->insert([
            [
                'notification_id' => 1,
                'sendin_status' => 'sent',
                'log_message' => 'Notification sent successfully.',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'notification_id' => 2,
                'sendin_status' => 'failed',
                'log_message' => 'Failed to send notification.',
                'created_at' => now(),
                'updated_at' => now()
            ]
        ]);
    }
}
