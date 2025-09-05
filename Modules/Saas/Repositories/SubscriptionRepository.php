<?php

namespace Modules\Saas\Repositories;

use App\Models\User;
use App\Models\Branch;
use App\Models\Tenant;
use App\Models\Upload;
use BaconQrCode\Writer;
use App\Models\Branding;
use App\Models\Role\Role;
use Illuminate\Support\Str;
use App\Models\Subscription;
use App\Models\Company\Company;
use App\Models\Settings\Currency;
use App\Models\Settings\Language;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Models\Settings\HrmLanguage;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Modules\Break\Entities\BreakType;
use App\Models\Hrm\Attendance\Weekend;
use Illuminate\Support\Facades\Config;
use App\Models\coreApp\Setting\Setting;
use BaconQrCode\Renderer\ImageRenderer;
use Illuminate\Support\Facades\Artisan;
use App\Models\Hrm\AppSetting\AppScreen;
use App\Repositories\CurrencyRepository;
use App\Repositories\LanguageRepository;
use Modules\Break\Entities\BreakSetting;
use Modules\Saas\Entities\SaasSubscription;
use Modules\Saas\Entities\UserTenantMapping;
use App\Models\coreApp\Setting\CompanyConfig;
use App\Helpers\CoreApp\Traits\PermissionTrait;
use BaconQrCode\Renderer\Image\SvgImageBackEnd;
use Modules\Saas\Events\SubscriptionApproveEvent;
use BaconQrCode\Renderer\RendererStyle\RendererStyle;
use Modules\Saas\Services\Backend\SubscriptionService;

class SubscriptionRepository
{
    use PermissionTrait;

    protected $subscription;
    protected $currencyRepository;
    protected $languageRepository;

    public function __construct(SaasSubscription $subscription)
    {
        $this->subscription       = $subscription;
        $this->currencyRepository = new CurrencyRepository();
        $this->languageRepository = new LanguageRepository();
    }

    function fields()
    {
        return [
            _trans('common.ID'),
            _trans('common.Company'),
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

        $data = $this->subscription->query()->with('status');
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

                if ($data->status_id == 2) {
                    $approveRoute = route('saas.subscriptions.approve', ['id' => $data->id]);
                    $action_button .= actionButton(_trans('common.Approve'), "approveSubscription('$approveRoute')", 'approve');

                    $rejectRoute = route('saas.subscriptions.reject', ['id' => $data->id]);
                    $action_button .= actionButton(_trans('common.Reject'), "rejectSubscription('$rejectRoute')", 'reject');
                }

                // if (hasPermission('branch_delete') && $data->is_main_company != 'yes') {
                //     $action_button .= actionButton(_trans('common.Delete'), '__globalDelete(' . $data->id . ',`hrm/branches/delete/`)', 'delete');
                // }

                $button = '';
                if ($action_button) {
                    $button = ' <div class="dropdown dropdown-action">
                                    <button type="button" class="btn-dropdown" data-bs-toggle="dropdown"
                                        aria-expanded="false">
                                        <i class="fa-solid fa-ellipsis"></i>
                                    </button>
                                    <ul class="dropdown-menu dropdown-menu-end">
                                    ' . $action_button . '
                                    </ul>
                                </div>';
                }
                

                $subscription = "";
                if ($data->is_subscription) {
                    $subscription .= '<div class="text-center"><i class="las la-check-circle fs-5 text-primary"></i></div>';
                }

                $planInfo = '<div class="d-flex flex-column gap-0"><span>' . @$data->pricingPlanPrice->pricingPlan->name . '</span><span>' . currency_format(number_format(@$data->pricingPlanPrice->price, 2)) . '</span></div>';

                $paymentStatus = '<small class="fw-bold text-' . @$data->paymentStatus->class . '">' . @$data->paymentStatus->name . '</small>';
                
                if($data->is_demo_checkout){
                    $paymentInfo = '<div class="d-flex align-items-center justify-content-between gap-1"><span>Demo Checkout</span>' . $paymentStatus . '</div>';
                } elseif($data->payment_gateway == 'Stripe'){
                    $paymentInfo = '<div class="d-flex flex-column gap-0"><div class="d-flex align-items-center justify-content-between gap-1"><span>' . $data->payment_gateway . '</span>' . $paymentStatus . '</div><span>$' . $data->trx_id . '</span></div>';
                } else{
                    $payment = $data->offline_payment ? json_decode(@$data->offline_payment)->payment_type : '';
                    $payment_type = $payment ? ucfirst(str_replace('_', ' ', $payment)) : _trans('N/A');
                    $paymentInfo = '<div class="d-flex flex-column gap-0"><div class="d-flex align-items-center justify-content-between gap-1"><span>' . $data->payment_gateway . '</span>' . $paymentStatus . '</div><span>Payment Type: ' . $payment_type . '</span></div>';
                }

                // $paymentInfo = $data->is_demo_checkout
                // ? '<div class="d-flex align-items-center justify-content-between gap-1"><span>Demo Checkout</span>' . $paymentStatus . '</div>'
                // : '<div class="d-flex flex-column gap-0"><div class="d-flex align-items-center justify-content-between gap-1"><span>' . $data->payment_gateway . '</span>' . $paymentStatus . '</div><span>$' . $data->trx_id . '</span></div>';

                return [
                    'id' => $data->id,
                    'company' => @$data->company->company_name,
                    'plan_info' => $planInfo,
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

    function subscriptionApprove($id)
    {
        $subscription =  SaasSubscription::find($id);
        $countSubscription = SaasSubscription::where('company_id', $subscription->company_id)->count();
        if(!config('app.single_db')){
            SaasSubscription::where('id', $id)->update(['is_processing_for_approve' => 1]);
        }
    
        if ($countSubscription > 1) {
           
            $this->upgradeSubscription($subscription);
            
            return _trans('common.Subscription has been approved successfully');
        } else {
            if (config('app.single_db')) {
                $this->newSubscription($id, $subscription);
            } else {
                event(new SubscriptionApproveEvent(['id' => $id, 'subscription' => $subscription]));
                return _trans('common.Your approval is currently in progress; kindly allow some time for the final approval to be processed.');
            }
        }
    }

    function newSubscription($id, $subscription)
    {
        try {
            $tenantSubdomain = Str::slug($subscription->company->subdomain);
            $centralDomain = @base_settings('company_domain');
            $tenantDomain = $tenantSubdomain . '.' . $centralDomain;

            if (config('app.single_db')) {
                DB::beginTransaction();
                $this->storeTenantThisDB($subscription, Company::where('id', $subscription->company_id)->first(), $tenantDomain);
            } else {
                $this->storeTenantNewDB($subscription, $tenantDomain);
            }

            SaasSubscription::where('id', $id)->update([
                'status_id' => 5,
                'is_processing_for_approve' => 0,
                'is_running_subscription' => 1
            ]);

            Company::where('id', $subscription->company_id)->update(['subdomain' => $tenantDomain, 'status_id' => 1]);

            copyFiles('tenant/' . subdomainName($tenantDomain));

            (new SubscriptionService)->sendSubscriptionApproveMail($subscription);

            if (config('app.single_db')) {
                DB::commit();
            }
        } catch (\Throwable $e) {
            if (config('app.single_db')) {
                DB::rollBack();
            }
            Log::error('newSubscription Error: ' . $e);
        }
    }

    protected function storeTenantNewDB($subscription, $tenantDomain)
    {
        if (!defined('STDIN')) {
            define('STDIN', fopen('php://stdin', 'r'));
        }

        // COMPANY INPUTS
        $input['subscription_id_in_main_company'] = $subscription->id;
        $input['saas_company_id'] = $subscription->company_id;
        $input['invoice_no'] = $subscription->invoice_no;
        $input['country_id'] = $subscription->company->country_id;
        $input['name'] = $subscription->company->name;
        $input['company_name'] = $subscription->company->company_name;
        $input['email'] = $subscription->company->email;
        $input['phone'] = $subscription->company->phone;
        $input['total_employee'] = $subscription->company->total_employee;
        $input['trade_licence_number'] = $subscription->company->trade_licence_number;
        $input['business_type'] = $subscription->company->business_type;
        $input['subdomain'] = $subscription->company->subdomain;
        $input['tenantDomain'] = $tenantDomain;
        $input['is_subscription'] = 1;

        // SUBSCRIPTION INPUTS
        $input['plan_name'] = @$subscription->pricingPlanPrice->pricingPlan->name ?? 'n/a';
        $input['price'] = $subscription->price;
        $input['payment_gateway'] = $subscription->payment_gateway;
        $input['trx_id'] = $subscription->trx_id;
        $input['offline_payment'] = $subscription->offline_payment;
        $input['employee_limit'] = $subscription->employee_limit;
        $input['is_employee_limit'] = $subscription->is_employee_limit;
        $input['expiry_date'] = $subscription->expiry_date;
        $input['features'] = $subscription->features;
        $input['features_key'] = $subscription->features_key;
        $input['is_demo_checkout'] = $subscription->is_demo_checkout;
        $input['subscription_source'] = $subscription->source;
        $input['payment_status_id'] = $subscription->payment_status_id;

        session()->put('input', $input);

        $tenant = Tenant::create(['id' => Str::slug($subscription->company->subdomain)]);
        $tenant->domains()->create(['domain' => $tenantDomain]);
        $tenant->save();

        UserTenantMapping::create([
            'company_id' => $subscription->company_id,
            'tenant_id' => $tenant->id,
            'email' => $subscription->company->email,
            'domain' => $tenantDomain,
        ]);
    }

    protected function storeTenantThisDB($subscription, $company, $tenantDomain)
    {
        $branch = $this->storeCompanyBranchData($company);

        $this->storeCompanyUserAndRoleData($company, $branch);

        $this->storeCompanySettingData($company);

        $this->storeCompanyUploadData($company);
        
        $this->storeCompanyConfigData($company);

        $this->storeCompanyAppScreenData($subscription, $company);

        $this->storeCompanyCurrencyData($company);

        $this->storeCompanyLanguageData($company);

        $this->storeBrandingsData($company, $branch);

        $this->storeBreakData($company, $branch, $tenantDomain);
    }

    protected function storeCompanyBranchData($company)
    {
        $branch = Branch::create([
            'name'          => $company->company_name,
            'address'       => "Unknown Street",
            'phone'         => $company->phone,
            'email'         => $company->email,
            'company_id'    => $company->id,
        ]);

        $weekdays = [
            'saturday', 'sunday', 'monday', 'tuesday', 'wednesday', 'thursday', 'friday',
        ];

        foreach ($weekdays as $day) {
            $isWeekend = 'no';
            if ($day == 'friday') {
                $isWeekend = 'yes';
            }
            Weekend::create([
                'name'       => $day,
                'is_weekend' => $isWeekend,
                'status_id'  => 1,
                'company_id' => $company->id,
                'branch_id'  => $branch->id,
            ]);
        }

        return $branch;
    }

    protected function storeCompanyUserAndRoleData($company, $branch)
    {
        try {
            $roles = [
                'admin',
                'hr',
                'staff',
            ];
    
            foreach ($roles as $key => $role) {
                $role = Role::firstOrCreate([
                    'name' => $role,
                    'slug' => $role,
                    'company_id' => $company->id,
                    'branch_id' => $branch->id,
                ], [
                    'permissions' => json_encode($this->customPermissions($role)),
                    'status_id' => 1,
                    'app_login' => 1,
                    'web_login' => 1,
                ]);
            }
        } catch (\Throwable $th) {
            Log::error('Role Create Error: ' . $th);
        }

        try {
            User::create([
                'name'                  => $company->name,
                'email'                 => $company->email,
                'phone'                 => $company->phone,
                'is_admin'              => 1,
                'is_hr'                 => 0,
                'role_id'               => Role::where('company_id', $company->id)->first()?->id,
                'company_id'            => $company->id,
                'branch_id'             => $branch->id,
                'country_id'            => $company->country_id,
                'permissions'           => json_encode($this->adminPermissions()),
                'password'              => Hash::make(12345678)
            ]);
        } catch (\Throwable $th) {
            Log::error('User Create Error: ' . $th);
        }
    }

    protected function storeCompanySettingData($company)
    {
        $settings = [
            'company_name'              => $company->company_name,
            'company_description'       => $company->company_name . ' believes in painting the perfect picture of your idea while maintaining industry standards.',
            
            'android_url'               => 'https://play.google.com/store/apps/details?id=com.worx24hour.hrm',
            'ios_url'                   => 'https://apps.apple.com/us/app/24hourworx/id1620313188',
            'language'                  => 'en',
            

            'default_theme'             => 'app_theme_4',
            

            'mail_mailer'               => 'smtp',
            'mail_host'                 => 'smtp.gmail.com',
            'mail_port'                 => '587',
            'mail_username'             => 'test@test.com',
            'mail_password'             => '1234512345',
            'mail_from_address'         => 'test@test.com',
            'mail_encryption'           => 'tls',
            'mail_from_name'            => $company->company_name,
            
            
            'file_system_driver'          => 'local',
            'aws_access_key_id'           => '',
            'aws_secret_access_key'       => '',
            'aws_default_region'          => '',
            'aws_bucket'                  => '',
            'aws_url'                     => '',
            'aws_endpoint'                => '',
            'aws_use_path_style_endpoint' => 0,


            'firebase_api_key'              => '',
            'firebase_auth_domain'          => '',
            'firebase_auth_database_url'    => '',
            'firebase_project_id'           => '',
            'firebase_storage_bucket'       => '',
            'firebase_messaging_sender_id'  => '',
            'firebase_app_id'               => '',
            'firebase_measurement_id'       => '',
            'firebase_auth_collection_name' => '',


            'geocoding_api_key'  => '',
            'geocoding_base_url' => 'https://maps.googleapis.com/maps/api/geocode/json',


            'pusher_app_id'      => '',
            'pusher_app_key'     => '',
            'pusher_app_secret'  => '',
            'pusher_app_cluster' => '',
            'pusher_app_cluster' => '',
        ];

        foreach($settings ?? [] as $name => $value) {
            Setting::firstOrCreate([
                'company_id' => $company->id,
                'name' => $name,
            ], [
                'value' => $value
            ]);
        }

        $companyDir = 'assets/tenant/' . subdomainName($company->subdomain);

        $settingFiles = [
            'company_logo_backend'      => $companyDir . '/logo/logo-white.png',
            'company_logo_frontend'     => $companyDir . '/logo/logo-dark.png',
            'company_logo_tenant'     => $companyDir . '/logo/logo-dark.png',
            'company_icon'              => $companyDir . '/logo/favicon.png',
            'ios_icon'                  => $companyDir . '/logo/favicon.png',
            'android_icon'              => $companyDir . '/logo/favicon.png',
            'app_theme_1'               => $companyDir . '/app-screen/screen-1.png',
            'app_theme_2'               => $companyDir . '/app-screen/screen-2.png',
            'app_theme_3'               => $companyDir . '/app-screen/screen-3.png',
            'app_theme_4'               => $companyDir . '/app-screen/screen-4.png',
            'backend_image'             => $companyDir . '/logo/background_image.png',
        ];

        foreach($settingFiles ?? [] as $name => $path) {
            $upload = Upload::firstOrCreate([
                'company_id'         => $company->id,
                'file_original_name' => $name,
                'file_name'          => $name . '.png',
                'img_path'           => $path,
                'big_path'           => $path,
                'small_path'         => $path,
                'thumbnail_path'     => $path,
            ], [
                'extension' => '.png',
                'type'      => 'png',
                'file_size' => 0,
                'width'     => 100,
                'height'    => 100,
            ]);

            Setting::firstOrCreate([
                'company_id' => $company->id,
                'name'       => $name,
            ], [
                'value' => $upload->id
            ]);
        }
    }

    protected function storeCompanyUploadData($company)
    {
        $companyDir = 'assets/tenant/' . subdomainName($company->subdomain);

        $uploads = [
            [
                'file_original_name' => 'background_image',
                'file_name'          => 'background_image.png',
                'img_path'           => $companyDir . '/logo/background_image.png',
                'big_path'           => $companyDir . '/logo/background_image.png',
                'small_path'         => $companyDir . '/logo/background_image.png',
                'thumbnail_path'     => $companyDir . '/logo/background_image.png',
                'extension'          => '.png',
                'type'               => 'png',
                'file_size'          => 0,
                'width'              => 100,
                'height'             => 100,
            ],
            [
                'file_original_name' => 'support',
                'file_name'          => 'support.png',
                'img_path'           => $companyDir . '/app-screen/support.png',
                'big_path'           => $companyDir . '/app-screen/support.png',
                'small_path'         => $companyDir . '/app-screen/support.png',
                'thumbnail_path'     => $companyDir . '/app-screen/support.png',
                'extension'          => '.png',
                'type'               => 'png',
                'file_size'          => 0,
                'width'              => 100,
                'height'             => 100,
            ],
            [
                'file_original_name' => 'attendance',
                'file_name'          => 'attendance.png',
                'img_path'           => $companyDir . '/app-screen/attendance.png',
                'big_path'           => $companyDir . '/app-screen/attendance.png',
                'small_path'         => $companyDir . '/app-screen/attendance.png',
                'thumbnail_path'     => $companyDir . '/app-screen/attendance.png',
                'extension'          => '.png',
                'type'               => 'png',
                'file_size'          => 0,
                'width'              => 100,
                'height'             => 100,
            ],
            [
                'file_original_name' => 'notice',
                'file_name'          => 'notice.png',
                'img_path'           => $companyDir . '/app-screen/notice.png',
                'big_path'           => $companyDir . '/app-screen/notice.png',
                'small_path'         => $companyDir . '/app-screen/notice.png',
                'thumbnail_path'     => $companyDir . '/app-screen/notice.png',
                'extension'          => '.png',
                'type'               => 'png',
                'file_size'          => 0,
                'width'              => 100,
                'height'             => 100,
            ],
            [
                'file_original_name' => 'expense',
                'file_name'          => 'expense.png',
                'img_path'           => $companyDir . '/app-screen/expense.png',
                'big_path'           => $companyDir . '/app-screen/expense.png',
                'small_path'         => $companyDir . '/app-screen/expense.png',
                'thumbnail_path'     => $companyDir . '/app-screen/expense.png',
                'extension'          => '.png',
                'type'               => 'png',
                'file_size'          => 0,
                'width'              => 100,
                'height'             => 100,
            ],
            [
                'file_original_name' => 'leave',
                'file_name'          => 'leave.png',
                'img_path'           => $companyDir . '/app-screen/leave.png',
                'big_path'           => $companyDir . '/app-screen/leave.png',
                'small_path'         => $companyDir . '/app-screen/leave.png',
                'thumbnail_path'     => $companyDir . '/app-screen/leave.png',
                'extension'          => '.png',
                'type'               => 'png',
                'file_size'          => 0,
                'width'              => 100,
                'height'             => 100,
            ],
            [
                'file_original_name' => 'approval',
                'file_name'          => 'approval.png',
                'img_path'           => $companyDir . '/app-screen/approval.png',
                'big_path'           => $companyDir . '/app-screen/approval.png',
                'small_path'         => $companyDir . '/app-screen/approval.png',
                'thumbnail_path'     => $companyDir . '/app-screen/approval.png',
                'extension'          => '.png',
                'type'               => 'png',
                'file_size'          => 0,
                'width'              => 100,
                'height'             => 100,
            ],
            [
                'file_original_name' => 'phonebook',
                'file_name'          => 'phonebook.png',
                'img_path'           => $companyDir . '/app-screen/phonebook.png',
                'big_path'           => $companyDir . '/app-screen/phonebook.png',
                'small_path'         => $companyDir . '/app-screen/phonebook.png',
                'thumbnail_path'     => $companyDir . '/app-screen/phonebook.png',
                'extension'          => '.png',
                'type'               => 'png',
                'file_size'          => 0,
                'width'              => 100,
                'height'             => 100,
            ],
            [
                'file_original_name' => 'visit',
                'file_name'          => 'visit.png',
                'img_path'           => $companyDir . '/app-screen/visit.png',
                'big_path'           => $companyDir . '/app-screen/visit.png',
                'small_path'         => $companyDir . '/app-screen/visit.png',
                'thumbnail_path'     => $companyDir . '/app-screen/visit.png',
                'extension'          => '.png',
                'type'               => 'png',
                'file_size'          => 0,
                'width'              => 100,
                'height'             => 100,
            ],
            [
                'file_original_name' => 'appointments',
                'file_name'          => 'appointments.png',
                'img_path'           => $companyDir . '/app-screen/appointments.png',
                'big_path'           => $companyDir . '/app-screen/appointments.png',
                'small_path'         => $companyDir . '/app-screen/appointments.png',
                'thumbnail_path'     => $companyDir . '/app-screen/appointments.png',
                'extension'          => '.png',
                'type'               => 'png',
                'file_size'          => 0,
                'width'              => 100,
                'height'             => 100,
            ],
            [
                'file_original_name' => 'break',
                'file_name'          => 'break.png',
                'img_path'           => $companyDir . '/app-screen/break.png',
                'big_path'           => $companyDir . '/app-screen/break.png',
                'small_path'         => $companyDir . '/app-screen/break.png',
                'thumbnail_path'     => $companyDir . '/app-screen/break.png',
                'extension'          => '.png',
                'type'               => 'png',
                'file_size'          => 0,
                'width'              => 100,
                'height'             => 100,
            ],
            [
                'file_original_name' => 'report',
                'file_name'          => 'report.png',
                'img_path'           => $companyDir . '/app-screen/report.png',
                'big_path'           => $companyDir . '/app-screen/report.png',
                'small_path'         => $companyDir . '/app-screen/report.png',
                'thumbnail_path'     => $companyDir . '/app-screen/report.png',
                'extension'          => '.png',
                'type'               => 'png',
                'file_size'          => 0,
                'width'              => 100,
                'height'             => 100,
            ],
        ];

        foreach($uploads ?? [] as $upload) {
            Upload::firstOrCreate([
                'company_id'         => $company->id,
                'file_original_name' => $upload['file_original_name'],
                'file_name'          => $upload['file_name'],
                'img_path'           => $upload['img_path'],
                'big_path'           => $upload['big_path'],
                'small_path'         => $upload['small_path'],
                'thumbnail_path'     => $upload['thumbnail_path'],
            ], [
                'extension' => '.png',
                'type'      => 'png',
                'file_size' => 0,
                'width'     => 100,
                'height'    => 100,
            ]);
        }        
    }

    protected function storeCompanyConfigData($company)
    {
        $config_data = [
            "date_format"                   => "d-m-Y",
            "time_format"                   => "h",
            "ip_check"                      => "0",
            "leave_assign"                  => "0",
            "currency_symbol"               => "$",
            "location_service"              => "0",
            "app_sync_time"                 => "",
            "live_data_store_time"          => "",
            "lang"                          => "en",
            "multi_checkin"                 => 0,
            "currency"                      => 2,
            "timezone"                      => "Asia/Dhaka",
            "currency_code"                 => "USD",
            'location_check'                => 0,
            'attendance_method'             => 'N',
            'google'                        => 'AIzaSyBVF8ZCdPLYBEC2-PCRww1_Q0Abe5GYP1c',
            'is_employee_passport_required' => 0,
            'is_employee_eid_required'      => 0,
            'leave_carryover'               => 0,
        ];
        
        foreach ($config_data as $key => $value) {
            CompanyConfig::firstOrCreate([
                'company_id' => $company->id,
                'key'        => $key,
            ], [
                'value' => $value,
            ]);
        }

        HrmLanguage::create([
            'company_id'  => $company->id,
            'language_id' => 19,
            'is_default'  => 1,
        ]);
    }
        
    protected function storeCompanyAppScreenData($subscription, $company)
    {        
        $subscriptionFeatures = json_decode($subscription?->features_key) ?? [];

        $menus = [ 
            'notice', 
            'expense', 
            'approval', 
            'phonebook',
            'break'
        ];
        
        if (in_array('support', $subscriptionFeatures)) {
            $menus[] = 'support';
        }
        if (in_array('attendance', $subscriptionFeatures)) {
            $menus[] = 'attendance';
        }
        if (in_array('tasks', $subscriptionFeatures)) {
            $menus[] = 'task';
        }
        if (in_array('leave', $subscriptionFeatures)) {
            $menus[] = 'leave';
            $menus[] = 'daily_leave';
        }
        if (in_array('payroll', $subscriptionFeatures)) {
            $menus[] = 'payroll';
        }
        if (in_array('meeting', $subscriptionFeatures)) {
            $menus[] = 'meeting';
        }
        if (in_array('appointment', $subscriptionFeatures)) {
            $menus[] = 'appointments';
        }
        if (in_array('visit', $subscriptionFeatures)) {
            $menus[] = 'visit';
        }
        if (in_array('report', $subscriptionFeatures)) {
            $menus[] = 'report';
        }

        if (isModuleActive('VideoConference') && in_array('conference', $subscriptionFeatures)) {
            $menus[] = 'conference';
            $menus[] = 'chat';
        }

        $companyDir = 'assets/tenant/' . subdomainName($company->subdomain);
        
        foreach ($menus as $key => $menu) {
            $iconName = $menu . '.png';
            AppScreen::firstOrCreate(
            [
                'company_id' => $company->id,
                'slug'       => $menu
            ],
            [
                'name'      => plain_text($menu),
                'position'  => $key + 1,
                'icon'      => $companyDir . "/app-screen/{$iconName}",
                'status_id' => 1,
            ]);
        }
    }

    protected function storeCompanyCurrencyData($company)
    {
        try {
            $currencies = $this->currencyRepository->getCurrencyData();

            foreach ($currencies as $currency) {
                Currency::firstOrCreate(
                    [
                        'name'       => $currency[1],
                        'code'       => $currency[2],
                        'symbol'     => $currency[3],
                        'company_id' => $company->id,
                    ]
                );
            }


        } catch (\Throwable $th) {
            Log::error($th->getMessage());
        }
    }

    protected function storeCompanyLanguageData($company)
    {
        try {
            $languages = $this->languageRepository->getLanguageData();

            foreach ($languages as $language) {
                Language::firstOrCreate(
                    [
                        'code'       => $language[1],
                        'name'       => $language[2],
                        'company_id' => $company->id,
                    ],
                    [
                        'native' => $language[3],
                        'rtl'    => $language[4],
                        'status' => $language[5],
                    ]
                );
            }

            $english = Language::where('company_id', Auth::user()->company_id)->where('code', 'en')->where("name", "English")->first();
            $english->status = 1;
            $english->is_default = 1;
            $english->save();

        } catch (\Throwable $th) {
            Log::error($th->getMessage());
        }
    }

    protected function storeBrandingsData($company, $branch)
    {
        try {
            $companyDir = 'assets/tenant/' . subdomainName($company->subdomain);

            $brandings = [
                ['name' => 'primaryColor', 'value' => '#FFFFFF'],
                ['name' => 'primaryLight', 'value' => '#2872c7'],
                ['name' => 'primaryDark', 'value' => '#1b64ab'],
                ['name' => 'cardBackgroundDefault', 'value' => '#FFFFFF'],
                ['name' => 'cardBackgroundSubdued', 'value' => '#F0F0F0'],
                ['name' => 'textPrimary', 'value' => '#000000'],
                ['name' => 'textSecondary', 'value' => '#666666'],
                ['name' => 'textTertiary', 'value' => '#999999'],
                ['name' => 'textHint', 'value' => '#BBBBBB'],
                ['name' => 'textDisabled', 'value' => '#DDDDDD'],
                ['name' => 'textInversePrimary', 'value' => '#FFFFFF'],
                ['name' => 'textInverseSecondary', 'value' => '#EEEEEE'],
                ['name' => 'textInverseTertiary', 'value' => '#CCCCCC'],
                ['name' => 'brandTextLink', 'value' => '#1E90FF'],
                ['name' => 'iconPrimary', 'value' => '#000000'],
                ['name' => 'iconSecondary', 'value' => '#666666'],
                ['name' => 'iconDisabled', 'value' => '#DDDDDD'],
                ['name' => 'iconInverse', 'value' => '#FFFFFF'],
                ['name' => 'iconAccent', 'value' => '#FFA500'],
                ['name' => 'iconNavigationBar', 'value' => '#000080'],
                ['name' => 'iconAccentInactive', 'value' => '#FFA07A'],
                ['name' => 'dividerPrimary', 'value' => '#CCCCCC'],
                ['name' => 'dividerInverse', 'value' => '#333333'],
                ['name' => 'buttonDefault', 'value' => '#007BFF'],
                ['name' => 'buttonSecondary', 'value' => '#6C757D'],
                ['name' => 'buttonInverse', 'value' => '#FFFFFF'],
                ['name' => 'buttonDisabled', 'value' => '#DDDDDD'],
                ['name' => 'container', 'value' => '#F8F9FA'],
                ['name' => 'containerSecondary', 'value' => '#E9ECEF'],
                ['name' => 'logo_url', 'value' => $companyDir . "/logo/branding-logo.png"],
                ['name' => 'app_name', 'value' => $company->company_name],
                ['name' => 'font_family', 'value' => 'Arial, sans-serif'],
            ];
    
            foreach ($brandings as $branding) {
                Branding::firstOrCreate([
                    'name'       => $branding['name'],
                    'value'      => $branding['value'],
                    'company_id' => $company->id,
                    'branch_id'  => $branch->id,
                ]);
            }
        } catch (\Throwable $th) {
            Log::error($th->getMessage());
        }
    }

    protected function storeBreakData($company, $branch, $tenantDomain)
    {
        try {
            $companyDir = 'assets/tenant/' . subdomainName($company->subdomain);

            $breakTypes = [
                [
                    'name'                  => 'Lunch Break',
                    'slug'                  => 'lunch-break',
                    'description'           => 'A break for lunch.',
                    'limit'                 => 1,
                    'limit_type'            => 'day',
                    'duration_type'         => 'hour',
                    'max_duration'          => 1,
                ],
                [
                    'name'                  => 'Tea/Coffee Break',
                    'slug'                  => 'tea-coffee-break',
                    'description'           => 'A short break for tea.',
                    'limit'                 => 3,
                    'limit_type'            => 'day',
                    'duration_type'         => 'minute',
                    'max_duration'          => 15,
                ],
                [
                    'name'                  => 'Dinner Break',
                    'slug'                  => 'dinner-break',
                    'description'           => 'A break for dinner.',
                    'limit'                 => 1,
                    'limit_type'            => 'day',
                    'duration_type'         => 'hour',
                    'max_duration'          => 1,
                ],
            ];

            foreach ($breakTypes as $breakType) {

                $icon   = Upload::firstOrCreate([
                            'img_path'  => $companyDir . '/break/' . $breakType['slug'] . '.png',
                            'type'      => 'png',
                            'extension' => '.png'
                        ]);

                BreakType::firstOrCreate([
                    'name'               => $breakType['name'],
                    'slug'               => $breakType['slug'],
                    'company_id'         => $company->id,
                    'branch_id'          => $branch->id,
                ], [
                    'description'        => $breakType['description'],
                    'is_remark_required' => @$breakType['is_remark_required'] ?? false,
                    'will_ask_next_meal' => @$breakType['will_ask_next_meal'] ?? false,
                    'status_id'          => 1,
                    'limit'              => $breakType['limit'],
                    'limit_type'         => $breakType['limit_type'],
                    'duration_type'      => $breakType['duration_type'],
                    'max_duration'       => $breakType['max_duration'],
                    'icon_id'            => $icon?->id ?? null,
                ]);
            }

            $encrypt_code = encrypt($tenantDomain);

            $renderer = new ImageRenderer(
                new RendererStyle(400),
                new SvgImageBackEnd()
            );
            $writer = new Writer($renderer);
            $qrCode = $writer->writeString($encrypt_code);

            $directory = public_path('uploads/qrcodes');
            if (!file_exists($directory)) {
                mkdir($directory, 0755, true);
            }

            $svgFilePath = 'uploads/qrcodes/' . uniqid() . '.svg';
            file_put_contents(public_path($svgFilePath), $qrCode);

            BreakSetting::firstOrCreate([
                'company_id'    => $company->id,
                'branch_id'     => $branch->id,
                'title'         => 'Break',
            ], [
                'path'          => $svgFilePath,
                'encrypt_text'  => $tenantDomain,
                'encrypt_code'  => $encrypt_code
            ]);

        } catch (\Throwable $th) {
            Log::error($th->getMessage());
        }
    }

    protected function upgradeSubscription($subscription)
    {
        $this->upgradeInMain($subscription);
    
        if(!config('app.single_db')){
            $this->upgradeInTenant($subscription);
        }
        
        (new SubscriptionService)->dispatchSubscriptionUpgradedJob($subscription);

        SaasSubscription::where('id', $subscription->id)->update(['is_processing_for_approve' => 0]);
    }

    protected function upgradeInMain($subscription)
    {
        if(config('app.single_db')){
          SaasSubscription::where('company_id', $subscription->company_id)->where('is_running_subscription',1)->update(
            ['is_running_subscription' => 0]
           );

           SaasSubscription::where('id', $subscription->id)->update(
            ['is_running_subscription' => 1]
           );
        }

        $subscription->update(['status_id' => 5]);

        
    }

    protected function upgradeInTenant($subscription)
    {
        $subdomain = explode('.', $subscription->company->subdomain);
        $subdomain = @$subdomain[0];

        // Switch to the main database connection
        config(['database.connections.mysql']);
        DB::reconnect('mysql');

        $db = config('tenancy.database.prefix') . $subdomain;

        // // Define your dynamic database configuration
        $databaseConfig = [
            'driver'    => 'mysql',
            'host'      => 'localhost',
            'database'  => $db,
            'username'  => env('DB_USERNAME'),
            'password'  => env('DB_PASSWORD'),
        ];

        // Set the configuration for the new connection
        Config::set('database.connections.dynamic_connection', $databaseConfig);
        
        \App\Models\Subscription::on('dynamic_connection')->update(['status_id' => 4]);
        $upgradeableSubcription = \App\Models\Subscription::on('dynamic_connection')->where('subscription_id_in_main_company', $subscription->id)->orderBy('id', 'DESC')->first();

        $upgradeableSubcription->update(['status_id' => 1]);
    }


    function subscriptionReject($id)
    {
        $subscription = SaasSubscription::find($id);
        $subscriptionCount = SaasSubscription::where('company_id', $subscription->company_id)->count();
       
        if ($subscriptionCount > 1) {
            $subdomain = explode('.', $subscription->company->subdomain);
            $subdomain = @$subdomain[0];
            if(config('app.single_db')){
               SaasSubscription::where('id',$id)->update(['status_id' => 6]);
               if ($subscriptionCount == 0){
                Company::where('id',$subscription->company_id)->update(['status_id' => 4]);
               }
            }
            else{
                config(['database.connections.mysql']);
                DB::reconnect('mysql');
        
                $db = config('tenancy.database.prefix') . $subdomain;
        
                $databaseConfig = [
                    'driver'    => 'mysql',
                    'host'      => 'localhost',
                    'database'  => $db,
                    'username'  => env('DB_USERNAME'),
                    'password'  => env('DB_PASSWORD'),
                ];
        
                Config::set('database.connections.dynamic_connection', $databaseConfig);
                Subscription::on('dynamic_connection')->where('subscription_id_in_main_company', $subscription->id)->update(['status_id' => 6]);
                if ($subscriptionCount == 0){
                    Company::on('dynamic_connection')->update(['status_id' => 4]);
                }
                
            }
    
        }else{

            $subscription->update(['status_id' => 6]);
            Company::where('id', $subscription->company_id)->update(['status_id' => 4]);
            (new SubscriptionService)->dispatchSubscriptionRejectJob($subscription);
        }
    
    }
}
