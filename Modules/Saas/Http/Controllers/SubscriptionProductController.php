<?php

namespace Modules\Saas\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\Saas\Http\Interfaces\SubscriptionProductInterface;

class SubscriptionProductController extends Controller
{

    private $productRepository;

    public function __construct(SubscriptionProductInterface $productRepository)
    {
        $this->productRepository = $productRepository;
    } 

    public function index(Request $request)
    {

        $items = $this->productRepository->getPaginatedItems();
        return view('subscription::products.index', [
            'title' => 'Subscription Product',
            'items' => $items,
            'menus' =>  getSubscriptionMenus(),
        ]);
    }

}
