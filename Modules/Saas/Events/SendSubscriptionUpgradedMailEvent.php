<?php

namespace Modules\Saas\Events;

use Illuminate\Queue\SerializesModels;

class SendSubscriptionUpgradedMailEvent
{
    use SerializesModels;

    protected $data;

    /**
     * Create a new event instance.
     */
    public function __construct($data)
    {
        $this->data = $data;
    }

    /**
     * Get the event data.
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * Get the channels the event should be broadcast on.
     */
    public function broadcastOn(): array
    {
        return [];
    }
}
