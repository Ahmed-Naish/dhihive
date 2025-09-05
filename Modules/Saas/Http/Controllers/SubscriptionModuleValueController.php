<?php

namespace Modules\Saas\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\Saas\Http\Interfaces\SubscriptionModuleValueInterface;

class SubscriptionModuleValueController extends Controller
{

    private $moduleValueRepository;

    public function __construct(SubscriptionModuleValueInterface $moduleValueRepository)
    {
        $this->moduleValueRepository = $moduleValueRepository;
    }
 

    public function index(Request $request)
    {

        $items = $this->moduleValueRepository->getPaginatedItems();
        return view('subscription::modules.values.index', [
            'title' => 'Subscription Module Values',
            'items' => $items,
            'menus' =>  getSubscriptionMenus(),
        ]);
    }

}
