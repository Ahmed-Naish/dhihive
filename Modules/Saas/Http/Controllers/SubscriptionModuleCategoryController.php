<?php

namespace Modules\Saas\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\Saas\Http\Interfaces\SubscriptionModuleCategoryInterface;

class SubscriptionModuleCategoryController extends Controller
{

    private $moduleCategoryRepository;

    public function __construct(SubscriptionModuleCategoryInterface $moduleCategoryRepository)
    {
        $this->moduleCategoryRepository = $moduleCategoryRepository;
    }

    /**
     * Display a listing of the resource.
     */

    public function getMenus()
    {
        return [
            [
                'label' => 'product',
                'url' => url('subscription/products'),
            ],
            [
                'label' => 'Subscription Payment Method',
                'url' => url('subscription/payment-methods'),
            ],
        ];
    }

    public function index(Request $request)
    {

        $items = $this->moduleCategoryRepository->getPaginatedItems();
        return view('subscription::modules.categories.index', [
            'title' => 'Subscription Module Categories',
            'items' => $items,
            'menus' =>  getSubscriptionMenus(),
        ]);
    }

}
