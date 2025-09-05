<?php

namespace Modules\Saas\Http\Controllers;

use App\Models\Content\AllContent;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\Saas\Entities\ContactMessage;
use Modules\Saas\Entities\PlanDurationType;
use Modules\Saas\Entities\PlanFeature;
use Modules\Saas\Entities\SaasCms;
use Modules\Saas\Entities\Subscriber;
use Modules\Saas\Services\Frontend\CheckoutService;

class SaasFrontendController extends Controller
{
    protected $checkoutService;

    public function __construct()
    {
        $this->checkoutService = new CheckoutService;
    }

    public function homePage()
    {
        $data['title'] = _trans('common.Home');
        $data['cms_contents'] = SaasCms::where('status_id', 1)->orderBy('order', 'ASC')->get();
        return view('saas::frontend.pages.index', compact('data'));
    }

    public function featurePage()
    {
        $data['title'] = _trans('common.Features');
        $data['breadcrumbs'] = [
            [
                'title' => 'Home',
                'links' => route('saas.homePage'),
                'is_last' => false,
            ],
            [
                'title' => 'Features',
                'links' => route('saas.featurePage'),
                'is_last' => true,
            ],
        ];

        $data['planFeatures'] = PlanFeature::where('status_id', 1)->get();

        return view('saas::frontend.pages.features', compact('data'));

    }

    public function content($slug)
    {
        $data['content_show'] = AllContent::where('slug', $slug)->first();
        $data['title'] = @$data['content_show']->title;
        $data['breadcrumbs'] = [
            [
                'title' => _trans('frontend.Home'),
                'links' => route('saas.homePage'),
                'is_last' => false,
            ],
            [
                'title' => $data['content_show']->title,
                'links' => route('saas.featurePage'),
                'is_last' => true,
            ],
        ];
        return view('saas::frontend.pages.content', compact('data'));
    }

    public function pricingPage()
    {
        $data['title']              = _trans('common.Pricing'); 
        $data['planDurationTypes']  = PlanDurationType::query()
                                    ->where('status_id', 1)
                                    ->whereHas('pricingPlanPrices', function ($q) {
                                        $q->where('status_id', 1)
                                        ->whereHas('pricingPlan', fn ($q) => $q->where('status_id', 1));
                                    })
                                    ->with(['pricingPlanPrices' => function ($q) {
                                        $q->where('status_id', 1)
                                        ->whereHas('pricingPlan', fn ($q) => $q->where('status_id', 1));
                                    }])
                                    ->get();

        $data['planFeatures']       = PlanFeature::where('status_id', 1)->pluck('title', 'id');

        $data['breadcrumbs'] = [
            [
                'title' => 'Home',
                'links' => route('saas.homePage'),
                'is_last' => false,
            ],
            [
                'title' => 'Pricing',
                'links' => route('saas.pricingPage'),
                'is_last' => true,
            ],
        ];

        return view('saas::frontend.pages.prices', compact('data'));
    }

    public function contactPage()
    {
        $data['title'] = _trans('common.Contact');
        $data['breadcrumbs'] = [
            [
                'title' => 'Home',
                'links' => route('saas.homePage'),
                'is_last' => false,
            ],
            [
                'title' => 'Contact Us',
                'links' => route('saas.contactPage'),
                'is_last' => true,
            ],
        ];
        
        return view('saas::frontend.pages.contact', compact('data'));
    }

    public function storeContact(Request $request)
    {
        try {
            ContactMessage::create([
                'subject' => $request->subject,
                'name' => $request->name,
                'email' => $request->email,
                'message' => $request->message
            ]);

            if ($request->is_subscribe) {
                Subscriber::firstOrCreate([
                    'email' => $request->email
                ]);
            }

            Toastr::success(_trans('alert.Message has been sent successfully'), 'Success');

            return redirect()->back();
        } catch (\Throwable $th) {
            return redirect()->back()->withError(_trans('alert.Something went wrong'));
        }
    }

    public function storeSubscriber(Request $request)
    {
        try {
            $subscriber = Subscriber::where('email', $request->email)->first();

            if ($subscriber) {
                Toastr::success(_trans('alert.Already Subscribed'), 'Success');
            } else {
                Subscriber::create([
                    'email' => $request->email
                ]);

                Toastr::success(_trans('alert.Subscribed successfully'), 'Success');
            }

            return redirect()->back();
        } catch (\Throwable $th) {
            return redirect()->back()->withError(_trans('alert.Something went wrong'));
        }
    }
}
