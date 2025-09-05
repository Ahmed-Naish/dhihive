<?php

namespace Modules\Saas\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\Saas\Http\Interfaces\SubscriptionUserPayoutMethodInterface;

class SubscriptionUserPayoutMethodController extends Controller
{

    private $userPayoutMethodRepository;

    public function __construct(SubscriptionUserPayoutMethodInterface $userPayoutMethodRepository)
    {
        $this->userPayoutMethodRepository = $userPayoutMethodRepository;
    }
 

    public function index(Request $request)
    {

        $items = $this->userPayoutMethodRepository->getPaginatedItems();
        return view('subscription::users.payouts.methods.index', [
            'title' => 'Subscription User Payout Methods',
            'items' => $items,
            'menus' =>  getSubscriptionMenus(),
        ]);
    }

}
