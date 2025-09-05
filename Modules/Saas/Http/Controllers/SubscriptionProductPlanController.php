<?php

namespace Modules\Saas\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\Saas\Http\Interfaces\SubscriptionProductPlanInterface;

class SubscriptionProductPlanController extends Controller
{

    private $productPlanRepository;

    public function __construct(SubscriptionProductPlanInterface $productPlanRepository)
    {
        $this->productPlanRepository = $productPlanRepository;
    }
 
    public function index(Request $request)
    {

        $items = $this->productPlanRepository->getPaginatedItems();
        return view('subscription::products.plans.index', [
            'title' => 'Subscription Product Plan',
            'items' => $items,
            'menus' =>  getSubscriptionMenus(),
        ]);
    }

}
