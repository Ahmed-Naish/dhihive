<?php

namespace Modules\Saas\Listeners;

use Illuminate\Support\Facades\Artisan;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Modules\Saas\Entities\SaasSubscription;
use Modules\Saas\Repositories\SubscriptionRepository;

class SubscriptionApproveListener implements ShouldQueue
{
    protected $subscriptionRepository;
    
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        $this->subscriptionRepository = new SubscriptionRepository(new SaasSubscription);
    }

    /**
     * Handle the event.
     */
    public function handle($event): void
    {
        Artisan::call('optimize:clear');
        $data = $event->getData();

        $this->subscriptionRepository->newSubscription($data['id'], $data['subscription']);
    }
}
