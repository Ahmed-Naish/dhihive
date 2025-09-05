<?php

namespace Modules\Saas\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\Saas\Http\Interfaces\SubscriptionModuleTermInterface;

class SubscriptionModuleTermController extends Controller
{

    private $moduleTermRepository;

    public function __construct(SubscriptionModuleTermInterface $moduleTermRepository)
    {
        $this->moduleTermRepository = $moduleTermRepository;
    }

      
    public function index(Request $request)
    {

        $items = $this->moduleTermRepository->getPaginatedItems();
        return view('subscription::modules.terms.index', [
            'title' => 'Subscription Module Terms',
            'items' => $items,
            'menus' =>  getSubscriptionMenus(),
        ]);
    }

}
