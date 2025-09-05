<?php

namespace Modules\Saas\Http\Controllers\Backend;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests\SubscriptionCurrencyRequest;
use App\Helpers\CoreApp\Traits\ApiReturnFormatTrait;

use App\Http\Requests\SubscriptionPaymentMethodRequest;
use Modules\Saas\Repositories\SubscriptionPaymentMethodRepositoryInterface;
use Modules\Saas\Entities\SubscriptionPaymentMethod;

class PaymentMethodController extends Controller
{
    use ApiReturnFormatTrait;

    protected $modelRepository;
    protected $title;
    protected $viwPath;
    protected $createPath;
    protected $createModalPath;
    protected $editPath;
    protected $className;

    public function __construct(SubscriptionPaymentMethodRepositoryInterface $modelRepository)
    {

        $this->modelRepository = $modelRepository;
        $this->title = _trans('subscription.Subscription Payment Method');
        $this->viwPath = "saas::backend.subscription.payment_methods.index";
        $this->createPath = "saas::backend.subscription.payment_methods.create";
        $this->createModalPath = "saas::backend.modal.create";
        $this->editPath = "saas::backend.subscription.payment_methods.edit";
        $this->className = "subscription_payment_methods_datatable";
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

    public function delete(SubscriptionPaymentMethod $paymentMethod)
    {
        return $this->modelRepository->destroy($paymentMethod);
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
                'url' => route('saas.subscription_payment_methods.store'),
                'attributes' => $this->modelRepository->createAttributes(),
                'button' => _trans('common.Save'),
            ]);
        } catch (\Throwable $th) {
            return response()->json('fail');
        }
    }

    public function store(SubscriptionPaymentMethodRequest $request)
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

    public function editModal($id)
    {
        $paymentMethod = $this->modelRepository->find($id);
        try {
            return view($this->createModalPath, [
                'title' => _trans('common.Edit') . ' ' . $this->title,
                'items' => $paymentMethod,
                'url' => route('saas.subscription_payment_methods.update', $paymentMethod->id),
                'attributes' => $this->modelRepository->editAttributes($paymentMethod),
                'button' => _trans('common.Update'),
            ]);
        } catch (\Throwable $th) {
            dd($th);
            return response()->json('failed', 400);
        }
    }

    public function update(SubscriptionPaymentMethodRequest $request, $id)
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
