<?php

namespace Modules\Notify\Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Modules\Notify\Entities\NotificationType;

class UserPreferenceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {

        $users = User::all();
        $notificationTypes = NotificationType::all();
        foreach ($users as $user) {
            foreach ($notificationTypes as $notificationType) {
                DB::table('jm_user_preferences')->insert([
                    'user_id' => $user->id,
                    'notification_type_id' => $notificationType->id,
                    'preference' => 'enabled',
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }
    }
}
