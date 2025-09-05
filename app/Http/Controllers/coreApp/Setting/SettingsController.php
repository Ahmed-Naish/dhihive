<?php

namespace App\Http\Controllers\coreApp\Setting;

use App\Models\User;
use App\Models\Upload;
use Illuminate\Http\Request;
use App\Models\Settings\Currency;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Support\Facades\Auth;
use App\Models\coreApp\Status\Status;
use App\Models\Permission\Permission;
use Illuminate\Support\Facades\Cache;
use App\Models\coreApp\Setting\Setting;
use App\Models\Database\DatabaseBackup;
use App\Repositories\CurrencyRepository;
use App\Helpers\CoreApp\Traits\FileHandler;
use App\Helpers\CoreApp\Traits\PermissionTrait;
use App\Repositories\Settings\SettingRepository;
use App\Helpers\CoreApp\Traits\ApiReturnFormatTrait;
use App\Repositories\Hrm\Leave\LeaveSettingRepository;
use App\Repositories\Settings\CompanyConfigRepository;

class SettingsController extends Controller
{

    use FileHandler, PermissionTrait, ApiReturnFormatTrait;

    protected LeaveSettingRepository $leaveSetting;
    protected $settingRepo;
    protected $companyConfigRepo;
    protected $currencyRepo;

    public function __construct(LeaveSettingRepository $leaveSettingRepository, SettingRepository $settingRepo,CurrencyRepository $currencyRepo, CompanyConfigRepository $companyConfigRepo)
    {
        $this->leaveSetting = $leaveSettingRepository;
        $this->settingRepo = $settingRepo;
        $this->companyConfigRepo = $companyConfigRepo;
        $this->currencyRepo = $currencyRepo;
    }

    public function index()
    {
        try {
            $data['title'] = _trans('settings.Settings');
            $data['databases'] = DatabaseBackup::orderByDesc('id')->get();
            return view('backend.settings.general.settings', compact('data'));
        } catch (\Exception $e) {
            return redirect()->back();
        }
    }
    public function newIndex()
    {
        try {
            $data['title'] = _trans('settings.Settings');
            $data['databases'] = DatabaseBackup::orderByDesc('id')->get();
            $data['settings'] = DB::table('settings')->where('company_id', auth()->user()->company_id)->get();

            return view('backend.settings.general.general_settings', compact('data'));
        } catch (\Exception $e) {
            return redirect()->route('manage.settings.currency_list');
        }
    }

    public function delete_currency($id){
        if (config('app.style') === 'demo' || env('APP_STYLE') === "demo") {
            Toastr::warning('You are not allowed to perform the delete action in demo mode.', 'Warning');
            return redirect()->back();
        }

        $currency = Currency::find($id);
        $currency->delete();
        Toastr::success(_trans('settings.Currency Deleted successfully'), 'Success');
        return redirect()->route('manage.settings.currency_list');
    }

    public function save_currency(Request $request)
    {
        if (config('app.style') === 'demo' || env('APP_STYLE') === "demo") {
            Toastr::warning('You are not allowed to perform the delete action in demo mode.', 'Warning');
            return redirect()->back();
        }

        $data  = new Currency();

        $data->name       = $request->name;
        $data->code       = $request->code;
        $data->symbol     = $request->symbol;
        $data->company_id = Auth::user()->company_id;
        $data->save();

        Toastr::success(_trans('settings.Currency Added successfully'), 'Success');
        return redirect()->route('manage.settings.currency_list');
    }

    public function update_currency(Request $request)
    {
        if (config('app.style') === 'demo' || env('APP_STYLE') === "demo") {
            Toastr::warning('You are not allowed to perform the delete action in demo mode.', 'Warning');
            return redirect()->back();
        }

            $currency = Currency::where("id", $request->id)->where('company_id', Auth::user()->company_id)->first();
            if ($currency) {
                $currency->name = $request->name;
                $currency->code = $request->code;
                $currency->symbol = $request->symbol;
                $currency->save();
            }

        Toastr::success(_trans('settings.Currency Updated successfully'), 'Success');
        return redirect()->route('manage.settings.currency_list');
    }

    public function edit_currency($id)
    {
        $data['title']    = _trans('settings.Edit Currency');
        $data['currency'] = Currency::where("id", $id)->where("company_id", Auth::user()->company_id)->first();

        return view('backend.settings.currency.edit', compact('data'));
    }

    public function currency_list(Request $request)
    {
        try {
            if ($request->ajax()) {
                return $this->currencyRepo->table($request);
            }
            $data['title'] = _trans('common.Currency List');
            $data['class'] = 'currency_table';
            $data['fields']     = $this->currencyRepo->fields();
            return view('backend.settings.currency.index', compact('data'));
        } catch (\Exception $exception) {
            dd($exception);
            Toastr::error(_trans('response.Something went wrong 11!'), 'Error');
            return back();
        }
    }

    public function add_currency()
    {
        $data['title'] = _trans('settings.Add Currency');
        return view('backend.settings.currency.create', compact('data'));
    }

    public function leaveSettings()
    {
        try {
            $data['title'] = _trans('leave.Leave');
            $data['leaveSetting'] = $this->leaveSetting->getLeaveSetting();
            return view('backend.settings.leave_settings.index', compact('data'));
        } catch (\Exception $e) {
            return redirect()->back();
        }
    }

    public function leaveSettingsEdit()
    {
        try {
            $data['title'] = _trans('leave.Leave');
            $data['leaveSetting'] = $this->leaveSetting->getLeaveSetting();
            return view('backend.settings.leave_settings.edit', compact('data'));
        } catch (\Exception $e) {
            return redirect()->back();
        }
    }

    public function emailSetup(Request $request)
    {
        if (config('app.style') === 'demo' || env('APP_STYLE') === "demo") {
            Toastr::warning('You are not allowed to perform the delete action in demo mode.', 'Warning');
            return redirect()->back();
        }

        $request = $request->except('_token');
        try {
            foreach ($request as $key => $value) {
                Setting::where('name', strtolower($key))->update(['value' => $value]);
                // putEnvConfigration($key, $value);
            }
            Toastr::success(_trans('settings.Email settings updated successfully'), 'Success');
            return redirect('/admin/settings/?email_setup=true');
        } catch (\Exception $e) {
            Toastr::error(_trans('response.Something went wrong!'), 'Error');
            return redirect()->back();
        }
    }

    public function firebaseSetup(Request $request)
    {
        if (config('app.style') === 'demo' || env('APP_STYLE') === "demo") {
            Toastr::warning('You are not allowed to perform the delete action in demo mode.', 'Warning');
            return redirect()->back();
        }

        $request = $request->except('_token');
        try {

            // Firebase Configuration
            // putEnvConfigration('FIREBASE_API_KEY', $request['firebase_api_key']);
            // putEnvConfigration('FIREBASE_AUTH_DOMAIN', $request['firebase_auth_domain']);
            // putEnvConfigration('FIREBASE_AUTH_DATABASE_URL', $request['firebase_auth_database_url']);
            // putEnvConfigration('FIREBASE_PROJECT_ID', $request['firebase_project_id']);
            // putEnvConfigration('FIREBASE_STORAGE_BUCKET', $request['firebase_storage_bucket']);
            // putEnvConfigration('FIREBASE_MESSAGING_SENDER_ID', $request['firebase_messaging_sender_id']);
            // putEnvConfigration('FIREBASE_APP_ID', $request['firebase_app_id']);
            // putEnvConfigration('FIREBASE_MEASUREMENT_ID', $request['firebase_measurement_id']);
            // putEnvConfigration('FIREBASE_AUTH_COLLECTION_NAME', $request['firebase_auth_collection_name']);
                        
            
            foreach ($request as $key => $value) {
                Setting::where('name', strtolower($key))->update(['value' => $value]);
            }
            Toastr::success(_trans('settings.Firebase settings updated successfully'), 'Success');
            return redirect('/admin/settings/?firebase_setup=true');
        } catch (\Exception $e) {
            Toastr::error(_trans('response.Something went wrong!'), 'Error');
            return redirect()->back();
        }
    }

    public function geocodingSetup(Request $request)
    {
        if (config('app.style') === 'demo' || env('APP_STYLE') === "demo") {
            Toastr::warning('You are not allowed to perform the delete action in demo mode.', 'Warning');
            return redirect()->back();
        }

        $request = $request->except('_token');
        try {

            // putEnvConfigration('GEOCODING_API_KEY', $request['geocoding_api_key']);
            // putEnvConfigration('GEOCODING_BASE_URL', $request['geocoding_base_url']);

            foreach ($request as $key => $value) {
                Setting::where('name', $key)->update(['value' => $value]);
            }
            Toastr::success(_trans('settings.Geocoding settings updated successfully'), 'Success');
            return redirect('/admin/settings/?geocoding_setup=true');
        } catch (\Exception $e) {
            Toastr::error(_trans('response.Something went wrong!'), 'Error');
            return redirect()->back();
        }
    }

    public function pusherSetup(Request $request)
    {
        // if (config('app.style') === 'demo' || env('APP_STYLE') === "demo") {
        //     Toastr::warning('You are not allowed to perform the delete action in demo mode.', 'Warning');
        //     return redirect()->back();
        // }

        $request = $request->except('_token');
        try {
            foreach ($request as $key => $value) {
                Setting::where('name', $key)->update(['value' => $value]);
            }
            Toastr::success(_trans('settings.Pusher settings updated successfully'), 'Success');
            return redirect('/admin/settings/?pusher_setup=true');
        } catch (\Exception $e) {
            Toastr::error(_trans('response.Something went wrong!'), 'Error');
            return redirect()->back();
        }
    }

    public function storageSetup(Request $request)
    {
        if (config('app.style') === 'demo' || env('APP_STYLE') === "demo") {
            Toastr::warning('You are not allowed to perform the delete action in demo mode.', 'Warning');
            return redirect()->back();
        }

        $request = $request->except('_token');
        try {
            // if ($request['file_system_driver']) {
            //     putEnvConfigration('FILESYSTEM_DRIVER', $request['file_system_driver']);
            //     putEnvConfigration('AWS_ACCESS_KEY_ID', $request['aws_access_key_id']);
            //     putEnvConfigration('AWS_SECRET_ACCESS_KEY', $request['aws_access_key_id']);
            //     putEnvConfigration('AWS_DEFAULT_REGION', $request['aws_default_region']);
            //     putEnvConfigration('AWS_BUCKET', $request['aws_bucket']);
            //     putEnvConfigration('AWS_URL', $request['aws_url']);
            //     putEnvConfigration('AWS_ENDPOINT', $request['aws_endpoint']);
            //     putEnvConfigration('AWS_USE_PATH_STYLE_ENDPOINT', $request['aws_use_path_style_endpoint']);
            // } else {
            //     putEnvConfigration('FILESYSTEM_DRIVER', $request['file_system_driver']);
            // }
            
            foreach ($request as $key => $value) {
                Setting::where('name', $key)->where('company_id', auth()->user()->company_id)->update(['value' => $value]);
            }
            Toastr::success(_trans('settings.Storage settings updated successfully'), 'Success');
            return redirect('/admin/settings/?storage_setup=true');
        } catch (\Exception $e) {
            Toastr::error(_trans('response.Something went wrong!'), 'Error');
            return redirect()->back();
        }
    }
    
    public function leaveSettingsUpdate(Request $request)
    {
        if (config('app.style') === 'demo' || env('APP_STYLE') === "demo") {
            Toastr::warning('You are not allowed to perform the delete action in demo mode.', 'Warning');
            return redirect()->back();
        }

        try {
            $this->leaveSetting->settingUpdate($request);
            Toastr::success(_trans('settings.Settings updated successfully'), 'Success');
            return redirect()->route('leaveSettings.view');
        } catch (\Exception $e) {
            Toastr::error(_trans('response.Something went wrong!'), 'Error');
            return redirect()->back();
        }
    }

    public function update(Request $request)
    {
        if (config('app.style') === 'demo' || env('APP_STYLE') === "demo") {
            Toastr::warning('You are not allowed to perform the delete action in demo mode.', 'Warning');
            return redirect()->back();
        }

        $request->validate([
            'company_description'   => 'nullable|max:255',
            'company_name'          => 'nullable|max:150',
            'android_url'           => 'nullable|max:255',
            'android_icon'          => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'ios_url'               => 'nullable|max:255',
            'ios_icon'              => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'company_icon'          => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'company_logo_frontend' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'company_logo_backend'  => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'backend_image'         => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'meta_title'            => 'nullable|max:255',
            'meta_description'      => 'nullable|max:500',
            'meta_keywords'         => 'nullable|max:500',
            'meta_image'            => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);
        
        $companyDir = 'assets/tenant/' . subdomainName(auth()->user()->company->subdomain);

        try {
            $settings = request()->except('_token');
            $i = 0;

            foreach ($settings as $key => $item) {
                if (config('app.single_db') && !isMainCompany() && request()->file($key)) {
                    $upload = Upload::find(base_settings($key));

                    if ($upload &&file_exists($upload->img_path)) {
                        unlink($upload->img_path);
                        Upload::where('id', base_settings($key))->delete();
                    }
                }

                $new_setup = Setting::where('name', $key)->where('company_id', auth()->user()->company_id)->first();
                if (!blank($new_setup)) {
                    $new_setup = Setting::where('name', $key)
                    ->where('company_id', auth()->user()->company_id)
                    ->update(['value' => $item]);
                } else {
                    $new_setup             = new Setting;
                    $new_setup->name       = $key;
                    $new_setup->value      = $item;
                    $new_setup->company_id = auth()->user()->company_id;
                    $new_setup->save();
                }

                //upgrade base app settings
                config()->set("settings.app.{$key}", $item);

                //change company name
                if ($key == 'company_name') {
                    // putEnvConfigration('APP_NAME', $item);
                }

                // Change language
                if ($key == 'language') {
                    App::setLocale($item);
                    session()->put('locale', $item);
                }

                if (request()->file($key)) {
                    if (config('app.single_db') && !isMainCompany()) {

                        $settings[$key] = $this->tenantUploadImage(request()->file($key), $companyDir .'/logo');
                    }else{
                        $settings[$key] = $this->uploadImage(request()->file($key), 'uploads/settings/logo');
                    }

                    Setting::where('name', $key)->where('company_id', auth()->user()->company_id)->update([
                        'value' => $settings[$key]->id,
                    ]);
                }

                $i++;
            }

            Toastr::success(_trans('settings.Settings updated successfully'), 'Success');
            return redirect('/admin/settings/?general_setting=true');
        } catch (\Throwable $th) {
            Log::error($th->getMessage());

            Toastr::error(_trans('response.Something went wrong!'), 'Error');
            return redirect()->back();
        }
    }

    public function permissionUpdate()
    {
        if (config('app.style') === 'demo' || env('APP_STYLE') === "demo") {
            Toastr::warning('You are not allowed to perform the delete action in demo mode.', 'Warning');
            return redirect()->back();
        }

        try {
            DB::beginTransaction();
            $delete_existing_permissions = Permission::truncate();
            $attributes = $this->adminRolePermissions();
            $user_permission_array = [];
            foreach ($attributes as $key => $attribute) {
                $permission = new Permission;
                $permission->attribute = $key;
                $permission->keywords = $attribute;
                $permission->save();
                foreach ($attribute as $key => $value) {
                    $user_permission_array[] = $value;
                }
            }
            $admin_permission = User::find(auth()->user()->id);
            $admin_permission->permissions = $user_permission_array;
            $admin_permission->save();
            DB::commit();
            Toastr::success(_trans('settings.Permission updated successfully'), 'Success');
            return redirect()->back();
        } catch (\Throwable $th) {
            DB::rollBack();
            Toastr::error(_trans('response.Something went wrong!'), 'Error');
            return redirect()->back();
        }
    }

    public function contactInfo(Request $request)
    {
        if (config('app.style') === 'demo' || env('APP_STYLE') === "demo") {
            Toastr::warning('You are not allowed to perform the delete action in demo mode.', 'Warning');
            return redirect()->back();
        }

        $request = $request->except('_token');
        try {
            foreach ($request as $key => $value) {
                DB::table('settings')->where('name', $key)->update(['value' => $value]);
            }
            Toastr::success(_trans('settings.Contact Info updated successfully'), 'Success');
            return redirect('/admin/settings/?contact_info=true');
        } catch (\Exception $e) {
            Toastr::error(_trans('response.Something went wrong!'), 'Error');
            return redirect()->back();
        }
    }

    public function paymentGateway(Request $request)
    {
        if (config('app.style') === 'demo' || env('APP_STYLE') === "demo") {
            Toastr::warning('You are not allowed to perform the delete action in demo mode.', 'Warning');
            return redirect()->back();
        }

        $request = $request->except('_token');
        try {
            foreach ($request as $key => $value) {
                DB::table('settings')->where('name', $key)->update(['value' => $value]);
            }

            if (!request()->filled('is_credit_card_payment')) {
                DB::table('settings')->where('name', 'is_credit_card_payment')->update(['value' => 0]);
            }

            if (!request()->filled('is_offline_payment')) {
                DB::table('settings')->where('name', 'is_offline_payment')->update(['value' => 0]);
            }

            if (!request()->filled('is_demo_checkout')) {
                DB::table('settings')->where('name', 'is_demo_checkout')->update(['value' => 0]);
            }

            // offline payment type
            if (!request()->filled('is_payment_type_cash')) {
                DB::table('settings')->where('name', 'is_payment_type_cash')->update(['value' => 0]);
            }
            if (!request()->filled('is_payment_type_cheque')) {
                DB::table('settings')->where('name', 'is_payment_type_cheque')->update(['value' => 0]);
            }
            if (!request()->filled('is_payment_type_bank_transfer')) {
                DB::table('settings')->where('name', 'is_payment_type_bank_transfer')->update(['value' => 0]);
            }

            Toastr::success(_trans('settings.Payment Gateway updated successfully'), 'Success');
            return redirect('/admin/settings/?payment_gateway=true');
        } catch (\Exception $e) {
            Toastr::error(_trans('response.Something went wrong!'), 'Error');
            return redirect()->back();
        }
    }

    public function websiteSettings(Request $request)
    {
        if (config('app.style') === 'demo' || env('APP_STYLE') === "demo") {
            Toastr::warning('You are not allowed to perform the delete action in demo mode.', 'Warning');
            return redirect()->back();
        }

        $request = $request->except('_token');
        try {
            foreach ($request as $key => $value) {
                DB::table('settings')->where('name', $key)->update(['value' => $value]);
            }

            if (!request()->filled('is_whatsapp_chat_enable')) {
                DB::table('settings')->where('name', 'is_whatsapp_chat_enable')->update(['value' => 0]);
            }

            if (!request()->filled('is_recaptcha_enable')) {
                DB::table('settings')->where('name', 'is_recaptcha_enable')->update(['value' => 0]);
            }

            if (!request()->filled('is_tawk_enable')) {
                DB::table('settings')->where('name', 'is_tawk_enable')->update(['value' => 0]);
            }

            $keys = ['isWhatsAppChatEnable', 'whatsAppChatNumber', 'isTawkEnable', 'tawkChatWidgetScript', 'isRecaptchaEnable', 'recaptchaSitekey', 'recaptchaSecret'];

            foreach ($keys as $key) {
                Cache::forget($key);
            }

            Toastr::success(_trans('settings.Website Settings updated successfully'), 'Success');
            return redirect('/admin/settings/?website_settings=true');
        } catch (\Exception $e) {
            Toastr::error(_trans('response.Something went wrong!'), 'Error');
            return redirect()->back();
        }
    }
}
