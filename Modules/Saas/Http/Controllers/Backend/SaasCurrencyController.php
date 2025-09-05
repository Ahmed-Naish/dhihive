<?php

namespace Modules\Saas\Http\Controllers\Backend;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests\SubscriptionCurrencyRequest;
use App\Helpers\CoreApp\Traits\ApiReturnFormatTrait;
use Modules\Saas\Entities\SubscriptionCurrency;
use Modules\Saas\Repositories\SubscriptionCurrencyRepositoryInterface;

class SaasCurrencyController extends Controller
{
    use ApiReturnFormatTrait;

    protected $modelRepository;
    protected $title;
    protected $viwPath;
    protected $createPath;
    protected $createModalPath;
    protected $editPath;
    protected $className;

    public function __construct(SubscriptionCurrencyRepositoryInterface $modelRepository)
    {
        $this->modelRepository = $modelRepository;
        $this->title = _trans('subscription.Subscription Currency');
        $this->viwPath = "saas::backend.subscription.currency.index";
        $this->createPath = "saas::backend.subscription.currency.create";
        $this->createModalPath = "saas::backend.modal.create";
        $this->editPath = "saas::backend.subscription.currency.edit";
        $this->className = "subscription_currency_table";
    }

    public function index(Request $request)
    {
        if ($request->ajax()) {
            return $this->modelRepository->table($request);
        }
        $data = [
            'class' => $this->className,
            'fields' => $this->modelRepository->fields(),
            'checkbox' => true,
            'title' => $this->title,
        ];
        return view($this->viwPath, compact('data'));
    }

    public function create()
    {

        return view($this->createPath, [
            'title' => _trans('common.Add') . ' ' . $this->title,
        ]);
    }
    

    public function edit(SubscriptionCurrency $currency)
    {
        return view($this->editPath, [
            'title' => _trans('common.Edit') . ' ' . $this->title,
            'items' => $currency,
        ]);
    }

    public function delete(SubscriptionCurrency $currency)
    {

        return $this->modelRepository->destroy($currency);
    }

    public function statusUpdate(Request $request)
    {
        return $this->modelRepository->statusUpdate($request);
    }

    public function deleteData(Request $request)
    {
        return $this->modelRepository->destroyAll($request);
    }

    public function createModal()
    {
        try {
            return view($this->createModalPath, [
                'title' => _trans('common.Create') . ' ' . $this->title,
                'url' => route('saas.subscription_currencies.store'),
                'attributes' => $this->modelRepository->createAttributes(),
                'button' => _trans('common.Save'),
            ]);
        } catch (\Throwable $th) {
            return response()->json('fail');
        }
    }

    public function store(SubscriptionCurrencyRequest $request)
    {
        try {
            if (!$request->ajax()) {
                Toastr::error(_trans('response.Please click on button!'), 'Error');
                return redirect()->back();
            }
            return $this->modelRepository->newStore($request);
        } catch (\Throwable $th) {
            return $this->responseWithError($th->getMessage(), [], 400);
        }
    }

    public function editModal( $id)
    {
        $currency = $this->modelRepository->find($id);
        try {
            return view($this->createModalPath, [
                'title' => _trans('common.Edit') . ' ' . $this->title,
                'items' => $currency,
                'url' => route('saas.subscription_currencies.update', $currency->id),
                'attributes' => $this->modelRepository->editAttributes($currency),
                'button' => _trans('common.Update'),
            ]);
        } catch (\Throwable $th) {

            return response()->json('failed', 400);
        }
    }

    public function update(SubscriptionCurrencyRequest $request, $id)
    {
        try {
            if (!$request->ajax()) {
                Toastr::error(_trans('response.Please click on button!'), 'Error');
                return redirect()->back();
            }
            return $this->modelRepository->newUpdate($request, $id);
        } catch (\Throwable $th) {
            return $this->responseWithError($th->getMessage(), [], 400);
        }
    }
}
