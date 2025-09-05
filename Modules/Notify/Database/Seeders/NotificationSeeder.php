<?php

namespace Modules\Notify\Database\Seeders;

use App\Models\User;
use Faker\Factory as Faker;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;
use Modules\Notify\Entities\Notification;
use Modules\Notify\Entities\NotificationType;

class NotificationSeeder extends Seeder
{ /**
    * Run the database seeds.
    *
    * @return void
    */
   public function run()
   {
       $faker = Faker::create();

       // Get all users and notification types
       $users = User::all();
       $notificationTypes = NotificationType::all();

       // Seed notifications
       foreach ($users as $user) {
           // Generate random number of notifications per user
           $numNotifications = rand(1, 5);

           for ($i = 0; $i < $numNotifications; $i++) {
               $senderId = $users->random()->id;
               $receiverId = $user->id;
               $notificationTypeId = $notificationTypes->random()->id;

               Notification::create([
                   'sender_id' => $senderId,
                   'receiver_id' => $receiverId,
                   'notification_type_id' => $notificationTypeId,
                   'message' => $faker->sentence,
                   //status can be 'pending', 'sent', 'failed'
                     'status' => $faker->randomElement(['pending', 'sent', 'failed']),
                   'scheduled_at' => $faker->dateTimeBetween('now', '+1 day'),
                   'company_id' => $user->company_id,
                   'branch_id' => $user->branch_id,
                   'created_at' => now(),
                   'updated_at' => now(),
               ]);
           }
       }
   }
}
