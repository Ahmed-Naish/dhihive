<?php

namespace Modules\Saas\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\Saas\Http\Interfaces\SubscriptionPaymentInterface;

class SubscriptionPaymentController extends Controller
{

    private $paymentRepository;

    public function __construct(SubscriptionPaymentInterface $paymentRepository)
    {
        $this->paymentRepository = $paymentRepository;
    }
 

    public function index(Request $request)
    {

        $items = $this->paymentRepository->getPaginatedItems();
        return view('subscription::payments.index', [
            'title' => 'Subscription Payment Methods',
            'items' => $items,
            'menus' =>  getSubscriptionMenus(),
        ]);
    }

}
