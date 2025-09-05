<?php

namespace Modules\Saas\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\Saas\Http\Interfaces\SubscriptionPackagePlanInterface;

class SubscriptionPackagePlanController extends Controller
{

    private $packagePlansRepository;

    public function __construct(SubscriptionPackagePlanInterface $packagePlansRepository)
    {
        $this->packagePlansRepository = $packagePlansRepository;
    }
 

    public function index(Request $request)
    {

        $items = $this->packagePlansRepository->getPaginatedItems();
        return view('subscription::packages.plans.index', [
            'title' => 'Subscription Package Plans',
            'items' => $items,
            'menus' =>  getSubscriptionMenus(),
        ]);
    }

}
