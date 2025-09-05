<?php

namespace Modules\Notify\Listeners;

use App\Events\EarlyCheckOutEvent;
use Illuminate\Queue\InteractsWithQueue;

class EarlyCheckOutListener
{
    use InteractsWithQueue;

    public function handle(EarlyCheckOutEvent $event)
    {
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
