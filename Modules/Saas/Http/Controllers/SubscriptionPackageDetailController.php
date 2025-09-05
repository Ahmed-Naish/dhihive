<?php

namespace Modules\Saas\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\Saas\Http\Interfaces\SubscriptionPackageDetailInterface;

class SubscriptionPackageDetailController extends Controller
{

    private $packageDetailsRepository;

    public function __construct(SubscriptionPackageDetailInterface $packageDetailsRepository)
    {
        $this->packageDetailsRepository = $packageDetailsRepository;
    } 

    public function index(Request $request)
    {

        $items = $this->packageDetailsRepository->getPaginatedItems();
        return view('subscription::packages.details.index', [
            'title' => 'Subscription Package Details',
            'items' => $items,
            'menus' =>  getSubscriptionMenus(),
        ]);
    }

}
