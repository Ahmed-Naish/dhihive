<?php

namespace Modules\Saas\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\Saas\Http\Interfaces\SubscriptionPackageInterface;

class SubscriptionPackageController extends Controller
{

    private $packageRepository;

    public function __construct(SubscriptionPackageInterface $packageRepository)
    {
        $this->packageRepository = $packageRepository;
    }

    public function index(Request $request)
    {

        $items = $this->packageRepository->getPaginatedItems();
        return view('subscription::packages.index', [
            'title' => 'Subscription Packages',
            'items' => $items,
            'menus' => getSubscriptionMenus(),
        ]);
    }

}
