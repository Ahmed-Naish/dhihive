<?php

namespace Modules\Saas\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\Saas\Http\Interfaces\SubscriptionModuleDetailInterface;

class SubscriptionModuleDetailController extends Controller
{

    private $moduleDetailRepository;

    public function __construct(SubscriptionModuleDetailInterface $moduleDetailRepository)
    {
        $this->moduleDetailRepository = $moduleDetailRepository;
    }

   

    public function index(Request $request)
    {

        $items = $this->moduleDetailRepository->getPaginatedItems();
        return view('subscription::modules.details.index', [
            'title' => 'Subscription Module Details',
            'items' => $items,
            'menus' =>  getSubscriptionMenus(),
        ]);
    }

}
