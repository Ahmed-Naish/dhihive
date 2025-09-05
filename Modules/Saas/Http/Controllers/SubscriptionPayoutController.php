<?php

namespace Modules\Saas\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\Saas\Http\Interfaces\SubscriptionPayoutInterface;

class SubscriptionPayoutController extends Controller
{

    private $payoutRepository;

    public function __construct(SubscriptionPayoutInterface $payoutRepository)
    {
        $this->payoutRepository = $payoutRepository;
    }
 

    public function index(Request $request)
    {

        $items = $this->payoutRepository->getPaginatedItems();
        return view('subscription::payouts.index', [
            'title' => 'Subscription Product',
            'items' => $items,
            'menus' =>  getSubscriptionMenus(),
        ]);
    }

}
