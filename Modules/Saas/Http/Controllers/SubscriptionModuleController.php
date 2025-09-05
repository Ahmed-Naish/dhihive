<?php

namespace Modules\Saas\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\Saas\Http\Interfaces\SubscriptionModuleInterface;

class SubscriptionModuleController extends Controller
{

    private $moduleRepository;

    public function __construct(SubscriptionModuleInterface $moduleRepository)
    {
        $this->moduleRepository = $moduleRepository;
    }

    /**
     * Display a listing of the resource.
     */

  

    public function index(Request $request)
    {

        $items = $this->moduleRepository->getPaginatedItems();
        return view('subscription::modules.index', [
            'title' => 'Subscription Modules',
            'items' => $items,
            'menus' =>  getSubscriptionMenus(),
        ]);
    }

}
