<?php

namespace Modules\Saas\Repositories;

use Carbon\Carbon;
use App\Models\Tenant;
use GuzzleHttp\Client;
use Illuminate\Support\Str;
use App\Models\Subscription;
use App\Models\Company\Company;
use Illuminate\Support\Facades\DB;
use App\Models\Hrm\Country\Country;
use Illuminate\Support\Facades\Log;
use App\Models\Traits\RelationCheck;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Mail;
use App\Models\Permission\Permission;
use Illuminate\Support\Facades\Config;
use Modules\Saas\Entities\PlanFeature;
use App\Mail\Hrm\NewTenantPasswordMail;
use Modules\Saas\Entities\EmailTemplate;
use App\Models\Hrm\Department\Department;
use Illuminate\Support\Facades\Validator;
use App\Models\Hrm\Designation\Designation;
use Modules\Saas\Entities\SaasSubscription;
use App\Helpers\CoreApp\Traits\PermissionTrait;
use Modules\Saas\Emails\SubscriptionSuccessMail;
use Modules\Saas\Services\Frontend\CheckoutService;
use App\Helpers\CoreApp\Traits\ApiReturnFormatTrait;
use Modules\Saas\Http\Requests\CheckoutStoreRequest;
use Modules\Saas\Repositories\SubscriptionRepository;
use App\Models\coreApp\Relationship\RelationshipTrait;
use Modules\Saas\Events\SendSubscriptionSuccessMailEvent;

class CompanyRepository
{
    use RelationshipTrait, RelationCheck, ApiReturnFormatTrait, PermissionTrait;

    protected $company;
    protected $subscription;
    protected $checkoutService;
    protected $subscriptionRepository;
    
    public function __construct(Company $company)
    {
        $this->company = $company;
        $this->checkoutService = new CheckoutService;
        $this->subscriptionRepository = new SubscriptionRepository(new SaasSubscription);
    }

    public function get($id)
    {
        return $this->company->query()->where('company_id', getCurrentCompany())->findOrFail($id);
    }

    public function getAll()
    {

        return $this->company->query()->where('company_id', getCurrentCompany())->where('status_id', 1)->where('id', '!=', '1')->get();
    }

    public function getPermission()
    {
        return Permission::get();
    }

    public function index()
    {
        // TODO: Implement index() method.
    }

    public function update($request, $id)
    {
        $company = $this->company->where(['id' => $id])->first();
        if (!$company) {
            return $this->responseWithError(_trans('message.Data not found'), 'id', 404);
        }
        try {
            $company->company_name = $request->company_name;
            $company->phone = $request->phone;
            $company->email = $request->email;
            $company->trade_licence_number = $request->trade_licence_number;
            $company->subdomain = $request->subdomain;
            $company->total_employee = $request->total_employee;
            $company->business_type = $request->business_type;
            $company->country_id = $request->country_id;
            $company->status_id = $request->status;
            $company->save();

            if (config('app.mood') == 'Saas') {
                $this->updateTenantCompanyStatus($company->subdomain, $request->status);
            }

        } catch (\Throwable $th) {}
    }

    // new functions
    function fields()
    {
        return [
            _trans('common.ID'),
            _trans('common.Name'),
            _trans('common.Phone'),
            _trans('common.Email'),
            _trans('common.Employee'),
            _trans('common.Trade Licence Number'),
            _trans('common.Sub Domain'),
            _trans('common.Subscription'),
            _trans('common.Status'),
            _trans('common.Action'),

        ];
    }

    // data table functions
    function table($request)
    {
        $data = $this->company->query()->with('status');
        $where = array();
        if ($request->search) {
            $where[] = ['company_name', 'like', '%' . $request->search . '%'];
        }

        if (config('app.mood') == 'Saas') {
            $where[] = ['id', '!=', 1];
        }

        $data = $data
            ->where($where)
            ->orderBy('id', 'DESC')
            ->paginate($request->limit ?? 10);
        return [
            'data' => $data->map(function ($data) {
                $action_button = '';
                if (hasPermission('company_update')) {
                    $action_button .= actionButton(_trans('common.Edit'), 'mainModalOpen(`' . route('saas.company.edit', $data->id) . '`)', 'modal');
                }

                if (hasPermission('company_delete') && $data->is_main_company != 'yes') {
                    $action_button .= actionButton(_trans('common.Delete'), '__globalDelete(' . $data->id . ',`admin/saas/companies/delete/`)', 'delete');
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
                $domainUrl = "";
                if ($data->subdomain != '') {
                    $domainUrl = $data->subdomain;
                }

                $subscription = "";
                if ($data->is_subscription) {
                    $expiry_date = SaasSubscription::where('company_id', $data->id)->where('status_id', 5)->orderBy('id', 'DESC')->first()?->expiry_date;
                    $subscription .= '<div class="d-flex flex-column"><span>'._trans('common.Start').': '. showDate($data->created_at) .'</span><span>'._trans('common.Expiry').': '. showDate($expiry_date) .'</span></div>';
                }

                $status = '<small class="badge badge-' . @$data->status->class . '">' . @$data->status->name . '</small>';

                $totalSubscription = SaasSubscription::where('company_id', $data->id)->count();
                $isRejected = SaasSubscription::where('company_id', $data->id)->where('status_id', 6)->count();
                
                if ($totalSubscription == 1 && $isRejected) {
                    $status .= '<br /><small class="badge badge-danger mt-1">' . _trans('common.Reject') . '</small>';
                }

                $subdomain = explode('.', $domainUrl);

                $isPending = false;

                if ($totalSubscription == 1) {
                    $isPending = SaasSubscription::where('company_id', $data->id)->first()?->status_id == 2 ? true : false;
                }

                // $processing = !$isPending && !@$subdomain[1] && $data->is_main_company == 'no' ? true : false;

                $subdomainURL = env('APP_HTTPS') ? 'https://' . $domainUrl : 'http://' . $domainUrl;

                return [
                    'id' => $data->id,
                    'name' => $data->company_name,
                    'phone' => $data->phone,
                    'email' => $data->email,
                    'employee' => $data->total_employee,
                    'trade_licence_number' => $data->trade_licence_number,
                    'subdomain' => $isPending ? _trans('common.Pending for approve') . '...' : '<a target="_blank" href="'.$subdomainURL.'">' . $domainUrl . '</a>',
                    'subscription' => $subscription,
                    'status' => $status,
                    'action' => $totalSubscription == 1 && $isRejected ? '' : $button,
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

    // data table functions
    function trashListTable($request)
    {
        $data = $this->company->query()->with('status')->onlyTrashed();

        $where = array();
        if ($request->search) {
            $where[] = ['company_name', 'like', '%' . $request->search . '%'];
        }

        if (config('app.mood') == 'Saas') {
            $where[] = ['id', '!=', 1];
        }

        $data = $data
        ->where($where)
        ->orderBy('id', 'DESC')
        ->paginate($request->limit ?? 10);

        return [
            'data' => $data->map(function ($data) {
                $action_button = '';
                if (hasPermission('company_update')) {
                    $action_button .= '<a href="javascript:;" class="dropdown-item" onclick="permanentDelete('. $data->id .')">
                            Restore
                        </a>';
                }

                if (hasPermission('company_delete') && $data->is_main_company != 'yes') {
                    $action_button .= actionButton(_trans('common.Permanent Delete'), '__globalDelete(' . $data->id . ',`admin/saas/companies/permanent-delete/`)', 'delete');
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
                $domainUrl = "";
                if ($data->subdomain != '') {
                    $domainUrl = $data->subdomain;
                }

                $subscription = "";
                if ($data->is_subscription) {
                    $expiry_date = SaasSubscription::where('company_id', $data->id)->where('status_id', 5)->orderBy('id', 'DESC')->first()?->expiry_date;
                    $subscription .= '<div class="d-flex flex-column"><span>' . _trans('common.Start') . ': ' . showDate($data->created_at) . '</span><span>' . _trans('common.Expiry') . ': ' . showDate($expiry_date) . '</span></div>';
                }

                $status = '<small class="badge badge-' . @$data->status->class . '">' . @$data->status->name . '</small>';

                $totalSubscription = SaasSubscription::where('company_id', $data->id)->count();
                $isRejected = SaasSubscription::where('company_id', $data->id)->where('status_id', 6)->count();

                if ($totalSubscription == 1 && $isRejected) {
                    $status .= '<br /><small class="badge badge-danger mt-1">' . _trans('common.Reject') . '</small>';
                }

                $subdomain = explode('.', $domainUrl);

                $isPending = false;

                if ($totalSubscription == 1) {
                    $isPending = SaasSubscription::where('company_id', $data->id)->first()?->status_id == 2 ? true : false;
                }

                // $processing = !$isPending && !@$subdomain[1] && $data->is_main_company == 'no' ? true : false;

                $subdomainURL = env('APP_HTTPS') ? 'https://' . $domainUrl : 'http://' . $domainUrl;

                return [
                    'id'                   => $data->id,
                    'name'                 => $data->company_name,
                    'phone'                => $data->phone,
                    'email'                => $data->email,
                    'employee'             => $data->total_employee,
                    'trade_licence_number' => $data->trade_licence_number,
                    'subdomain'            => $isPending ? _trans('common.Pending for approve') . '...' : '<a target="_blank" href="' . $subdomainURL . '">' . $domainUrl . '</a>',
                    'subscription'         => $subscription,
                    'status'               => $status,
                    'action'               => $totalSubscription == 1 && $isRejected ? '' : $button,
                ];
            }),
            'pagination' => [
                'total'           => $data->total(),
                'count'           => $data->count(),
                'per_page'        => $data->perPage(),
                'current_page'    => $data->currentPage(),
                'total_pages'     => $data->lastPage(),
                'pagination_html' => $data->links('backend.pagination.custom')->toHtml(),
            ],
        ];
    }

    public function destroy($id)
    {
        try {
            DB::beginTransaction();

            $company = $this->company->with(
                [
                    "users:id,company_id",
                    "branches:id,company_id",
                    "roles:id,company_id"
                ]
            )->find($id);

            if (!$company) {
                throw new \Throwable("Company not found");
            }

            // Update company deleted by
            $company->deleted_by = Auth::id();
            $company->status_id  = 4;
            $company->save();

            // Delete uses, branch, roles
            $company->users()->delete();
            $company->branches()->delete();
            $company->roles()->delete();

            // Delete department
            DB::table('departments')
            ->where('company_id', $company->id)
            ->update(['deleted_at' => Carbon::now()]);

            // Delete designation
            DB::table('designations')
            ->where('company_id', $company->id)
            ->update(['deleted_at' => Carbon::now()]);

            // Delete the company
            $company->delete();

            DB::commit();

            return true;
        } catch (\Throwable $th) {
            DB::rollBack();
            throw $th;
        }
    }

    public function restore($id)
    {
        try {
            DB::beginTransaction();

            // Restore the company
            $company = $this->company->withTrashed()->find($id);

            if (!$company) {
                throw new \Throwable("Company not found");
            }

            $company->restore();

            // Restore users, branches, and roles
            $company->users()->withTrashed()->restore();
            $company->branches()->withTrashed()->restore();
            $company->roles()->withTrashed()->restore();

            // Restore departments
            DB::table('departments')
            ->where('company_id', $company->id)
            ->whereNotNull('deleted_at')
            ->update(['deleted_at' => null]);

            // Restore designations
            DB::table('designations')
            ->where('company_id', $company->id)
            ->whereNotNull('deleted_at')
            ->update(['deleted_at' => null]);

            // Update company information
            $company->restored_at = Carbon::now();
            $company->restored_by = Auth::id();
            $company->save();

            DB::commit();

            return true;
        } catch (\Throwable $th) {
            DB::rollBack();
            throw $th;
        }
    }

    public function trashListCount()
    {
        return $this->company->onlyTrashed()->count();
    }

    public function permanentDelete($id)
    {
        try {
            DB::beginTransaction();

            $company = $this->company->with(
                [
                    "users:id,company_id",
                    "branches:id,company_id",
                    "roles:id,company_id"
                ]
            )->withTrashed()->find($id);

            if (!$company) {
                throw new \Throwable("Company not found");
            }

            // Permanently delete users, branches, and roles
            $company->users()->forceDelete();
            $company->branches()->forceDelete();
            $company->roles()->forceDelete();

            // Permanently delete departments
            DB::table('departments')
            ->where('company_id', $company->id)
            ->delete();

            // Permanently delete designations
            DB::table('designations')
            ->where('company_id', $company->id)
            ->delete();

            // Permanently delete the company
            $company->forceDelete();

            DB::commit();

            return true;
        } catch (\Throwable $th) {
            DB::rollBack();
            throw $th;
        }
    }

    // statusUpdate
    public function statusUpdate($request)
    {
        try {
            if (@$request->action == 'active') {
                $company = $this->company->whereIn('id', $request->ids)->update(['status_id' => 1]);
                return $this->responseWithSuccess(_trans('message.Company activate successfully.'), $company);
            }
            if (@$request->action == 'inactive') {
                $company = $this->company->whereIn('id', $request->ids)->update(['status_id' => 4]);
                return $this->responseWithSuccess(_trans('message.Company inactivate successfully.'), $company);
            }
            return $this->responseWithError(_trans('message.Company inactivate failed'), [], 400);
        } catch (\Throwable $th) {
            return $this->responseWithError($th->getMessage(), [], 400);
        }
    }

    public function destroyAll($request)
    {
        try {
            if (@$request->ids) {
                $branch = $this->company->where('company_id', getCurrentCompany())->whereIn('id', $request->ids)->update(['deleted_at' => now()]);
                return $this->responseWithSuccess(_trans('message.Branch activate successfully.'), $branch);
            } else {
                return $this->responseWithError(_trans('message.Branch not found'), [], 400);
            }
        } catch (\Throwable $th) {
            return $this->responseWithError($th->getMessage(), [], 400);
        }
    }

    //new functions

    function createAttributes()
    {
        return [
            'name' => [
                'field' => 'input',
                'type' => 'text',
                'required' => true,
                'id' => 'name',
                'class' => 'form-control ot-form-control ot-input',
                'col' => 'col-md-12 form-group mb-3',
                'label' => _trans('common.Owner Name'),
            ],
            'company_name' => [
                'field' => 'input',
                'type' => 'text',
                'required' => true,
                'id' => 'company_name',
                'class' => 'form-control ot-form-control ot-input',
                'col' => 'col-md-12 form-group mb-3',
                'label' => _trans('common.Company Name'),
            ],
            'phone' => [
                'field' => 'input',
                'type' => 'text',
                'required' => true,
                'id' => 'phone',
                'class' => 'form-control ot-form-control ot-input',
                'col' => 'col-md-12 form-group mb-3',
                'label' => _trans('common.Phone'),
            ],
            'email' => [
                'field' => 'input',
                'type' => 'text',
                'required' => true,
                'id' => 'email',
                'class' => 'form-control ot-form-control ot-input',
                'col' => 'col-md-12 form-group mb-3',
                'label' => _trans('common.Email'),
            ],
            'trade_licence_number' => [
                'field' => 'input',
                'type' => 'text',
                'required' => true,
                'id' => 'trade_licence_number',
                'class' => 'form-control ot-form-control ot-input',
                'col' => 'col-md-12 form-group mb-3',
                'label' => _trans('common.Trade License Number'),
            ],
            'subdomain' => [
                'field' => 'input',
                'type' => 'text',
                'required' => true,
                'id' => 'subdomain',
                'class' => 'form-control ot-form-control ot-input',
                'col' => 'col-md-12 form-group mb-3',
                'label' => _trans('common.Sub Domain'),
            ],
            'total_employee' => [
                'field' => 'input',
                'type' => 'number',
                'required' => true,
                'id' => 'total_employee',
                'class' => 'form-control ot-form-control ot-input',
                'col' => 'col-md-12 form-group mb-3',
                'label' => _trans('common.Total Employee'),
            ],
            'business_type' => [
                'field' => 'input',
                'type' => 'text',
                'required' => true,
                'id' => 'business_type',
                'class' => 'form-control ot-form-control ot-input',
                'col' => 'col-md-12 form-group mb-3',
                'label' => _trans('common.Business Type'),
            ],
            'country_id' => [
                'field' => 'select',
                'type' => 'select',
                'required' => true,
                'id' => 'country',
                'class' => 'form-select select2-input ot-input mb-3 modal_select2',
                'col' => 'col-md-12 form-group mb-3',
                'label' => _trans('common.Country'),
                'options' => Country::get()->map(function ($country) {
                    return [
                        'text' => $country->name,
                        'value' => $country->id,
                        'active' => false,
                    ];
                }),
            ],
            'status' => [
                'field' => 'select',
                'type' => 'select',
                'required' => true,
                'id' => 'status',
                'class' => 'form-select select2-input ot-input mb-3 modal_select2',
                'col' => 'col-md-12 form-group mb-3',
                'label' => _trans('common.Status'),
                'options' => [
                    [
                        'text' => _trans('payroll.Active'),
                        'value' => 1,
                        'active' => true,
                    ],
                    [
                        'text' => _trans('payroll.Inactive'),
                        'value' => 4,
                        'active' => false,
                    ],
                ],
            ],

        ];
    }
    function editAttributes($companyModel)
    {
        return [

            'id' => [
                'field' => 'input',
                'type' => 'hidden',
                'id' => 'id',
                'value' => $companyModel->id,
            ],
            'company_name' => [
                'field' => 'input',
                'type' => 'text',
                'required' => true,
                'id' => 'name',
                'class' => 'form-control ot-form-control ot-input',
                'col' => 'col-md-12 form-group mb-3',
                'label' => _trans('common.Company Name'),
                'value' => $companyModel->company_name,
            ],
            'phone' => [
                'field' => 'input',
                'type' => 'text',
                'required' => true,
                'id' => 'phone',
                'class' => 'form-control ot-form-control ot-input',
                'col' => 'col-md-12 form-group mb-3',
                'label' => _trans('common.Phone'),
                'value' => $companyModel->phone,
            ],
            'email' => [
                'field' => 'input',
                'type' => 'text',
                'required' => true,
                'id' => 'email',
                'class' => 'form-control ot-form-control ot-input',
                'col' => 'col-md-12 form-group mb-3',
                'label' => _trans('common.Email'),
                'value' => $companyModel->email,
            ],
            'trade_licence_number' => [
                'field' => 'input',
                'type' => 'text',
                'required' => true,
                'id' => 'trade_licence_number',
                'class' => 'form-control ot-form-control ot-input',
                'col' => 'col-md-12 form-group mb-3',
                'label' => _trans('common.Trade Licence Number'),
                'value' => $companyModel->trade_licence_number,
            ],
            'subdomain' => [
                'field' => 'input',
                'type' => 'text',
                'required' => true,
                'id' => 'subdomain',
                'class' => 'form-control ot-form-control ot-input',
                'col' => 'col-md-12 form-group mb-3',
                'label' => _trans('common.Sub Domain'),
                'value' => $companyModel->subdomain,
                'readonly' => true
            ],
            'total_employee' => [
                'field' => 'input',
                'type' => 'text',
                'required' => true,
                'id' => 'total_employee',
                'class' => 'form-control ot-form-control ot-input',
                'col' => 'col-md-12 form-group mb-3',
                'label' => _trans('common.Total Employee'),
                'value' => $companyModel->total_employee,
            ],
            'business_type' => [
                'field' => 'input',
                'type' => 'text',
                'required' => true,
                'id' => 'business_type',
                'class' => 'form-control ot-form-control ot-input',
                'col' => 'col-md-12 form-group mb-3',
                'label' => _trans('common.Business Type'),
                'value' => $companyModel->business_type,
            ],
            'status' => [
                'field' => 'select',
                'type' => 'select',
                'required' => true,
                'id' => 'status',
                'class' => 'form-select select2-input ot-input mb-3 modal_select2',
                'col' => 'col-md-12 form-group mb-3',
                'label' => _trans('common.Status'),
                'options' => [
                    [
                        'text' => _trans('payroll.Active'),
                        'value' => 1,
                        'active' => $companyModel->status_id == 1 ? true : false,
                    ],
                    [
                        'text' => _trans('payroll.Inactive'),
                        'value' => 4,
                        'active' => $companyModel->status_id == 4 ? true : false,
                    ],
                ],
            ],
            'country_id' => [
                'field' => 'select',
                'type' => 'select',
                'required' => true,
                'id' => 'country',
                'class' => 'form-select select2-input ot-input mb-3 modal_select2',
                'col' => 'col-md-12 form-group mb-3',
                'label' => _trans('common.Country'),
                'options' => Country::get()->map(function ($country) use ($companyModel) {
                    return [
                        'text' => $country->name,
                        'value' => $country->id,
                        'active' => $country->id == $companyModel->country_id ? true : false,
                    ];
                }),
            ],

        ];
    }

    function newStore($request)
    {
        // define('STDIN', fopen("php://stdin", "r"));

        $validator = Validator::make(request()->all(), [
            'email' => 'required|unique:companies,email',
            'phone' => 'required|unique:companies,phone',
            'subdomain' => 'required|unique:companies,subdomain',
        ], [
            'email.required' => 'The email field is required.',
            'email.unique' => 'The email already exists.',
            'phone.required' => 'The phone field is required.',
            'phone.unique' => 'The phone already exists.',
            'subdomain.required' => 'The subdomain field is required.',
            'subdomain.unique' => 'The subdomain already exists.',
        ]);

        if ($validator->fails()) {
            return $this->responseWithError($validator->errors()->first(), 400);
        }

        try {

            DB::beginTransaction();
            try {
                $company = new $this->company();
                $company->name = $request->name;
                $company->company_name = $request->company_name;
                $company->email = $request->email;
                $company->phone = $request->phone;
                $company->total_employee = $request->total_employee;
                $company->business_type = $request->business_type;
                $company->trade_licence_number = $request->trade_licence_number;
                $company->country_id = $request->country_id;
                $company->subdomain = $request->subdomain;
                $company->save();

                $features           = [];
                $features_key       = [];

                $planFeatures = PlanFeature::where('status_id', 1)->get();

                foreach($planFeatures ?? [] as $planFeature) {
                    $features[]     = $planFeature->title;
                    $features_key[] = $planFeature->key;
                }

                $this->subscription = SaasSubscription::create([
                    'invoice_no'            => str()->upper(str()->random(8)),
                    'company_id'            => $company->id,
                    'is_employee_limit'     => 0,
                    'features'              => json_encode($features),
                    'features_key'          => json_encode($features_key),
                    'source'                => 'Admin',
                    'status_id'             => 5,
                    'payment_status_id'     => 8
                ]);

                DB::commit();

                (new SubscriptionRepository(new SaasSubscription))->subscriptionApprove($this->subscription->id);
                $message = _trans('message.Company has been created successfully.') . ' ' . _trans('message.Company approval is currently in progress; kindly allow some time for the final approval to be processed.');
                return $this->responseWithSuccess($message, $company);

            } catch (\Illuminate\Database\QueryException $ex) {
                return $this->responseWithError($ex->getMessage(), [], 400);
            } catch (\Throwable $th) {
                DB::rollBack();
                return $this->responseWithError($th->getMessage(), [], 400);
            }

            

        } catch (\Throwable $e) {

            Log::error($e);
            return $this->responseWithError($e->getMessage(), [], 400);
        }
    }

    function storeCompany(CheckoutStoreRequest $request)
    {
        set_time_limit(300);
        
        try {
            if ($request->payment_gateway == 'Stripe') {
                $this->checkoutService->payWithStripe($request);
            }

            $this->checkoutService->storeSubscriptionCompany($request);

            $this->subscription = $this->checkoutService->storeSubscription($request);

            $subscriptionSuccessTemplate = EmailTemplate::where('slug', 'subscription-success')->first();
            $content = str_replace('[name]', $request->name, $subscriptionSuccessTemplate->content);
            $content = str_replace('[saas_admin_company]', @base_settings('company_name'), $content);
            $data = [
                'email'     => @$this->subscription->company->email,
                'subject'   => $subscriptionSuccessTemplate->subject,
                'content'   => $content
            ];
            
            if (config('app.single_db')) {
                try {
                    Mail::to($data['email'])->send(new SubscriptionSuccessMail($data));
                } catch (\Throwable $th) {
                    Log::error('SubscriptionSuccessMail Error: ' . $th);
                }
            } else {
                event(new SendSubscriptionSuccessMailEvent($data));
            }

            $this->subscriptionRepository->subscriptionApprove($this->subscription->id);
            
        } catch (\Throwable $th) {
            Toastr::error($th->getMessage(), 'Error');
        }
    }

    function updateTenantCompanyStatus($subdomain, $status_id)
    {
        $client         = new Client();
        $subdomainURL   = env('APP_HTTPS') ? 'https://' . $subdomain : 'http://' . $subdomain;
        $apiUrl         =  $subdomainURL . '/api/saas/v1/toggle-tenant-company-status';

        $client->request('POST', $apiUrl, [
            'form_params' => [
                'status' => $status_id == 1 ? 4 : 1
            ],
        ]);
    }
}
