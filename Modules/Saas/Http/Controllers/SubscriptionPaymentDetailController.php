<?php

namespace Modules\Saas\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\Saas\Http\Interfaces\SubscriptionPaymentDetailInterface;

class SubscriptionPaymentDetailController extends Controller
{

    private $paymentDetailRepository;

    public function __construct(SubscriptionPaymentDetailInterface $paymentDetailRepository)
    {
        $this->paymentDetailRepository = $paymentDetailRepository;
    }
 

    public function index(Request $request)
    {

        $items = $this->paymentDetailRepository->getPaginatedItems();
        return view('subscription::payments.details.index', [
            'title' => 'Subscription Payment Details',
            'items' => $items,
            'menus' =>  getSubscriptionMenus(),
        ]);
    }

}
