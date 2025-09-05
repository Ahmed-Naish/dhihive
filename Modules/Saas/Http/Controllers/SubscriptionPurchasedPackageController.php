<?php

namespace Modules\Saas\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\Saas\Http\Interfaces\SubscriptionPurchasedPackageInterface;

class SubscriptionPurchasedPackageController extends Controller
{

    private $purchasedPackageRepository;

    public function __construct(SubscriptionPurchasedPackageInterface $purchasedPackageRepository)
    {
        $this->purchasedPackageRepository = $purchasedPackageRepository;
    }
 

    public function index(Request $request)
    {

        $items = $this->purchasedPackageRepository->getPaginatedItems();
        return view('subscription::purchased.packages.index', [
            'title' => 'Subscription Purchased Packages',
            'items' => $items,
            'menus' =>  getSubscriptionMenus(),
        ]);
    }

}
