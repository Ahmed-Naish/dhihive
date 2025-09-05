<?php

namespace Modules\Notify\Listeners;

use Illuminate\Queue\InteractsWithQueue;
use Modules\Notify\Events\LateCheckInEvent;

class LateCheckInListener
{
    use InteractsWithQueue;

    public function handle(LateCheckInEvent $event)
    {
        $user = $event->user;

        // Perform actions for late check-in, e.g., send notifications, log events, etc.
        // Example: Notify admin or log the occurrence
        \Log::info("User {$user->name} checked in late at {$event->checkInTime}");


        $user = $event->user;

        // Perform actions for early check-out, e.g., send notifications, log events, etc.
        // Example: Notify admin or log the occurrence
        \Log::info("User {$user->name} checked out early at {$event->checkOutTime}");

        $notification = Notification::create([
            'sender_id' => 1,
            'receiver_id' => 2,
            'message' => 'Your notification message here.',
            'status' => 'pending',
            'scheduled_at' => now(),
            'status_id' => 1,
            'company_id' => 1,
            'branch_id' => 1,
        ]);

        // Assuming you have a log associated with this notification
        $notification->logs()->create([
            'sendin_status' => 'sent',
            'log_message' => 'Notification sent successfully.',
            'status_id' => 1,
        ]);

        // Assuming you have receipts associated with this notification
        $notification->receipts()->create([
            'user_id' => 2,
            'seen' => 0,
            'status_id' => 1,
        ]);

    }
}
