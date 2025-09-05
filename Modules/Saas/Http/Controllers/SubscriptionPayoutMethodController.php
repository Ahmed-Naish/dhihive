<?php

namespace Modules\Saas\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\Saas\Http\Interfaces\SubscriptionPayoutMethodInterface;

class SubscriptionPayoutMethodController extends Controller
{

    private $payoutMethodRepository;

    public function __construct(SubscriptionPayoutMethodInterface $payoutMethodRepository)
    {
        $this->payoutMethodRepository = $payoutMethodRepository;
    }
 
    public function index(Request $request)
    {

        $items = $this->payoutMethodRepository->getPaginatedItems();
        return view('subscription::payouts.methods.index', [
            'title' => 'Subscription Product',
            'items' => $items,
            'menus' =>  getSubscriptionMenus(),
        ]);
    }

}
