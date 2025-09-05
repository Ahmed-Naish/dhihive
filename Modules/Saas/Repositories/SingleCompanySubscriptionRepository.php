<?php

namespace Modules\Saas\Repositories;

use App\Models\Subscription;
use Modules\Saas\Entities\SaasSubscription;

class SingleCompanySubscriptionRepository
{
    protected $subscription, $saas_subscription;

    public function __construct(Subscription $subscription, SaasSubscription $saas_subscription)
    {
        $this->subscription = $subscription;
        $this->saas_subscription = $saas_subscription;
    }

    function fields()
    {
        return [
            _trans('common.ID'),
            _trans('common.Plan Info'),
            _trans('common.Employee Limit'),
            _trans('common.Expiry Date'),
            _trans('common.Payment Info'),
            _trans('common.Status'),
            _trans('common.Action'),

        ];
    }

    function table($request)
    {
        $currentDomainCompany = getCurrentDomainCompany();

        if(config('app.single_db')){
            $data = $this->saas_subscription->query()->with('status');
            $data->when($currentDomainCompany->is_main_company === 'no', function ($query) {
                return $query->where('company_id', auth()->user()->company_id)->with('status');
            });
        }else{
            $data = $this->subscription->query()->with('status');
        }

        $where = array();
        if ($request->search) {
            $where[] = ['name', 'like', '%' . $request->search . '%'];
        }
        $data = $data
            ->where($where)
            ->orderBy('id', 'DESC')
            ->paginate($request->limit ?? 10);

        return [
            'data' => $data->map(function ($data) {

                $action_button = '';

                if (hasPermission('subscription_invoice')) {
                    $action_button .= actionButton(_trans('common.Invoice'), route('single-company.subscriptions.invoice', ['id' => $data->id]));
                }

                $button = ' <div class="dropdown dropdown-action">
                                        <button type="button" class="btn-dropdown" data-bs-toggle="dropdown"
                                            aria-expanded="false">
                                            <i class="fa-solid fa-ellipsis"></i>
                                        </button>
                                        <ul class="dropdown-menu dropdown-menu-end">
                                        ' . $action_button . '
                                        </ul>
                                    </div>';

                $subscription = "";
                if ($data->is_subscription) {
                    $subscription .= '<div class="text-center"><i class="las la-check-circle fs-5 text-primary"></i></div>';
                }

                $paymentStatus = '<small class="fw-bold text-' . @$data->paymentStatus->class . '">' . @$data->paymentStatus->name . '</small>';

                if ($data->is_demo_checkout) {
                    $paymentInfo = '<div class="d-flex align-items-center justify-content-between gap-1"><span>Demo Checkout</span>' . $paymentStatus . '</div>';
                } elseif ($data->payment_gateway == 'Stripe') {
                    $paymentInfo = '<div class="d-flex flex-column gap-0"><div class="d-flex align-items-center justify-content-between gap-1"><span>' . $data->payment_gateway . '</span>' . $paymentStatus . '</div><span>$' . $data->trx_id . '</span></div>';
                } else {
                    $payment = json_decode(@$data->offline_payment)->payment_type;
                    $payment_type = $payment ? ucfirst(str_replace('_', ' ', $payment)) : _trans('N/A');
                    $paymentInfo = '<div class="d-flex flex-column gap-0"><div class="d-flex align-items-center justify-content-between gap-1"><span>' . $data->payment_gateway . '</span>' . $paymentStatus . '</div><span>Payment Type: ' . $payment_type . '</span></div>';
                }

                // $paymentInfo = $data->is_demo_checkout
                // ? '<div class="d-flex align-items-center justify-content-between gap-1"><span>Demo Checkout</span>' . $paymentStatus . '</div>'
                // : '<div class="d-flex flex-column gap-0"><div class="d-flex align-items-center justify-content-between gap-1"><span>' . $data->payment_gateway . '</span>' . $paymentStatus . '</div><span>$' . $data->trx_id . '</span></div>';

                return [
                    'id' => $data->id,
                    'plan_info' => config('app.single_db') ? @$data->pricingPlanPrice->pricingPlan->name  : $data->plan_name,
                    'employee_limit' => $data->is_employee_limit ? $data->employee_limit : _trans('Unlimited'),
                    'expiry_date' => showDate($data->expiry_date),
                    'status' => '<small class="badge badge-' . @$data->status->class . '">' . @$data->status->name . '</small>',
                    'payment_info' => $paymentInfo,
                    'action' => $button,
                ];
            }),
            'pagination' => [
                'total' => $data->total(),
                'count' => $data->count(),
                'per_page' => $data->perPage(),
                'current_page' => $data->currentPage(),
                'total_pages' => $data->lastPage(),
                'pagination_html' => $data->links('backend.pagination.custom')->toHtml(),
            ],
        ];
    }
}
