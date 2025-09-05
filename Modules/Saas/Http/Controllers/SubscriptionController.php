<?php

namespace Modules\Saas\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Log;
use Nwidart\Modules\Facades\Module;
use Illuminate\Contracts\Support\Renderable;
use Modules\Saas\Entities\SaasSubscription;
use Modules\Saas\Repositories\SubscriptionRepository;

class SubscriptionController extends Controller
{
    protected $subscriptionRepository;

    public function __construct(SubscriptionRepository $subscriptionRepository)
    {
        $this->subscriptionRepository = $subscriptionRepository;
    }

    /**
     * Display a listing of the resource.
     * @return Renderable
     */
    public function index(Request $request)
    {
        $tableName = $request->table_name;
        $moduleName = "Subscription";

        $data[0]="";
        $data[1]="";
        if($tableName != ""){
            $modelName = str_replace('_', '', ucwords($tableName, '_'));
            $makeModelCommand = "php artisan module:make-model {$modelName} --migration ".  $moduleName;
            $makeMigrationCommand = "php artisan module:make-migration create_{$tableName}_table ".  $moduleName;
            $data[0]=$makeModelCommand;
            $data[1]=$makeMigrationCommand;
        }

        return view('subscription::index', compact('data'));
    }

    public function list(Request $request)
    {
        if ($request->ajax()) {
            return $this->subscriptionRepository->table($request);
        }
        $data['title'] = _trans('common.Subscriptions');
        $data['class'] = 'subscription_table';
        $data['fields'] = $this->subscriptionRepository->fields();
        // $data['checkbox'] = true;
        return view('saas::backend.subscription.list', compact('data'));
    }

    public function approve($id)
    {
        set_time_limit(300);
        
        try {
            $message = $this->subscriptionRepository->subscriptionApprove($id);
            return response()->json([
                'status'    => 1,
                'message'   => $message,
            ]);

        } catch (\Throwable $th) {
           
            return response()->json([
                'status'    => 0,
                'message'   => $th->getMessage(),
            ]);
        }
    }

    public function reject($id)
    {
        try {
            $this->subscriptionRepository->subscriptionReject($id);
            
            return response()->json([
                'status'    => 1,
                'message'   => _trans('common.Successfully Rejected'),
            ]);

        } catch (\Throwable $th) {
            Log::info('aererer');
            Log::info($th->getMessage());
            dd($th);
            return response()->json([
                'status'    => 0,
                'message'   => $th->getMessage(),
            ]);
        }
    }

    public function home(){
        return view('subscription::home');
    }

    /**
     * Show the form for creating a new resource.
     * @return Renderable
     */
    public function create()
    {
        return view('subscription::create');
    }

    /**
     * Store a newly created resource in storage.
     * @param Request $request
     * @return Renderable
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Show the specified resource.
     * @param int $id
     * @return Renderable
     */
    public function show($id)
    {
        return view('subscription::show');
    }

    /**
     * Show the form for editing the specified resource.
     * @param int $id
     * @return Renderable
     */
    public function edit($id)
    {
        return view('subscription::edit');
    }

    /**
     * Update the specified resource in storage.
     * @param Request $request
     * @param int $id
     * @return Renderable
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     * @param int $id
     * @return Renderable
     */
    public function destroy($id)
    {
        //
    }
    
    public function fetchProcessingSubscriptions()
    {
        if (request()->ajax()) {
            $processingSubscriptions = SaasSubscription::whereHas('company', fn ($q) => $q->where('is_main_company', 'no'))->processingForApprove()->get();

            $view = View('saas::backend.subscription.processing-subscriptions', compact('processingSubscriptions'))->render();

            return response()->json(['status' => 1, 'html' => $view, 'isShow' => count($processingSubscriptions) ? true : false]);
        }

        return response()->json(['status' => 0, 'html' => null, 'isShow' => false]);
    }
}
