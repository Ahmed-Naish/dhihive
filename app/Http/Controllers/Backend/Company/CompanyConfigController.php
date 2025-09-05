<?php

namespace App\Http\Controllers\Backend\Company;

use App\Models\Branding;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;
use App\Models\coreApp\Setting\DateFormat;
use App\Helpers\CoreApp\Traits\FileHandler;
use App\Repositories\HrmLanguageRepository;
use App\Repositories\Settings\ApiSetupRepository;
use App\Helpers\CoreApp\Traits\ApiReturnFormatTrait;
use App\Repositories\Settings\CompanyConfigRepository;

class CompanyConfigController extends Controller
{
    use FileHandler, ApiReturnFormatTrait;

    protected  $config_repo;
    protected $apiSetupRepo;
    protected $hrmLanguageRepo;

    public function __construct(CompanyConfigRepository $companyConfigRepo, ApiSetupRepository $apiSetupRepo, HrmLanguageRepository $hrmLanguageRepo)
    {
        $this->config_repo = $companyConfigRepo;
        $this->apiSetupRepo = $apiSetupRepo;
        $this->hrmLanguageRepo = $hrmLanguageRepo;
    }

    public function index()
    {
        try {
            $data['title']    = _trans('settings.Settings');
            $configs          = $this->config_repo->getConfigs();
            $config_array     = [];
            foreach ($configs as $key => $config) {
                $config_array[$config->key] = $config->value;
            }
            $data['configs']   = $config_array;
            $data['timezones'] = $this->config_repo->time_zone();
            $data['currencies'] = $this->config_repo->currencies();
            $data['hrm_languages'] = $this->hrmLanguageRepo->with('language')->get();
            $data['date_formats'] = DateFormat::get();
            return view('backend.settings.general.company_settings', compact('data'));
        } catch (\Exception $e) {
            Toastr::error(_trans('response.Something went wrong!'), 'Error');
            return redirect()->back();
        }
    }
    // activation
    public function activation()
    {
        try {
            $data['title']    = _trans('settings.Activation');
            $configs          = $this->config_repo->getConfigs();
            $config_array     = [];
            foreach ($configs as $key => $config) {
                $config_array[$config->key] = $config->value;
            }
            $data['configs']   = $config_array;
            $data['timezones'] = $this->config_repo->time_zone();
            $data['currencies'] = $this->config_repo->currencies();
            $data['hrm_languages'] = $this->hrmLanguageRepo->get();
            $data['date_formats'] = DateFormat::get();
            return view('backend.company_setup.activation', compact('data'));
        } catch (\Exception $e) {
            Toastr::error(_trans('response.Something went wrong!'), 'Error');
            return redirect()->back();
        }
    }
    // configuration
    public function configuration()
    {
        try {
            $data['title']    = _trans('settings.Configuration');
            $configs          = $this->config_repo->getConfigs();
            $config_array     = [];
            foreach ($configs as $key => $config) {
                $config_array[$config->key] = $config->value;
            }
            $data['configs']   = $config_array;
            $data['timezones'] = $this->config_repo->time_zone();
            $data['currencies'] = $this->config_repo->currencies();
            $data['hrm_languages'] = $this->hrmLanguageRepo->get();
            $data['date_formats'] = DateFormat::get();

            $data['attendance_method'] = ['NORMAL' => _trans('attendance.Normal')];

            if (isModuleActive('FaceAttendance')) {
                $data['attendance_method']['FACE_RECOGNITION'] = _trans('attendance.Face Recognition') . ' (' . _trans('common.Pro') . ')';
            }

            if (isModuleActive('QrBasedAttendance')) {
                $data['attendance_method']['QRCODE'] = _trans('attendance.QRCODE') . ' (' . _trans('common.Pro') . ')';
            }

            if (isModuleActive('SelfieBasedAttendance')) {
                $data['attendance_method']['SELFIE'] = _trans('attendance.SELFIE') . ' (' . _trans('common.Pro') . ')';
            }

            return view('backend.company_setup.configuration', compact('data'));
        } catch (\Exception $e) {
            Toastr::error(_trans('response.Something went wrong!'), 'Error');
            return redirect()->back();
        }
    }


    public function update(Request $request)
    {
        if (config('app.style') === 'demo' || env('APP_STYLE') === "demo") {
            Toastr::warning('You are not allowed to perform this action in demo mode.', 'Warning');
            return redirect()->back();
        }

        try {
            $data = $request->except('_token');
            $result = $this->config_repo->update($data);
            if ($result === true) :
                Toastr::success(_trans('settings.Settings updated successfully'), 'Success');
            else :
                Toastr::error(_trans('response.Something went wrong!'), 'Error');
            endif;
            return redirect()->back();
        } catch (\Exception $e) {
            Toastr::error(_trans('response.Something went wrong!'), 'Error');
            return redirect()->back();
        }
    }

    //currencyInfo
    public function currencyInfo(Request $request)
    {
        $data = $request->except('_token');
        // echo "<pre>";print_r($this->config_repo->currencyInfo($data));exit;
        return  $this->config_repo->currencyInfo($data);
    }
    public function locationApi()
    {
        $data = [];
        $data['title'] = _trans('settings.API Setup');
        $data['company_apis'] = $this->apiSetupRepo->get();
        return view('backend.settings.general.api_setup', compact('data'));
    }
    public function updateApi(Request $request)
    {

        $data = request()->except('_token');
        $update = $this->apiSetupRepo->update($data);
        if ($update) {
            Toastr::success(_trans('settings.API setup updated successfully'), 'Success');
            return redirect()->back();
        } else {
            Toastr::error(_trans('response.Something went wrong!'), 'Error');
            return redirect()->back();
        }
    }

    public function brandings(Request $request)
    {
        if ($request->method() == 'POST') {

            if (!hasPermission('branding_update')) {
                abort(403);
            }

            if (demoCheck()) {
                return redirect()->back();
            }

            try {
                $data = $request->except('_token');
                if ($request->has('logo')) {

                    $branding = Branding::where(currentCompanyCurrentBranch())->where('name', 'logo_url')->first();
                    
                    if ($branding && file_exists($branding->value)) {
                        try {
                            $this->deleteFile($branding->value);
                            unlink($branding->value);
                        } catch (\Throwable $th) {}
                    }

                    $uploadImage = $this->uploadImage(request()->file('logo'), 'uploads/brandings/logo');
                    $data['logo_url'] = $uploadImage->img_path;
                    $data['logo'] = $uploadImage->id;
                }

                foreach ($data as $name => $value) {
                    Branding::updateOrCreate([
                        'name'          => $name,
                        'company_id'    => userCompanies(),
                    ], [
                        'value'         => $value
                    ]);
                }

                Cache::forget('branding_data');
                Toastr::success(_trans('settings.Branding settings updated successful.ly'), 'Success');
                return redirect()->back();
            } catch (\Exception $e) {
                Toastr::error(_trans('response.Something went wrong!'), 'Error');
                return redirect()->back();
            }
        } else {
            $data['title'] = _trans('settings.Brandings');
            $data['fontFamilies'] = $this->getGoogleFonts();
            $data['brandings'] = Branding::where(currentCompanyCurrentBranch())->pluck('value', 'name');
            return view('backend.company_setup.branding')->with($data);
        }
    }


    private function getGoogleFonts()
    {
        $apiKey = env('GOOGLE_FONTS_API_KEY');
        if (isset($apiKey)) {
            $fonts = Cache::get('google_fonts_list');
            if (!$fonts) {
                // Request to retrieve all fonts
                $response = Http::get('https://www.googleapis.com/webfonts/v1/webfonts?key=' . $apiKey);
                $fontFamilies = $response->json('items');
                // Extract font families only
                $fonts = array_map(function ($font) {
                    return $font['family'];
                }, $fontFamilies);
                // Cache the results for a day
                Cache::put('google_fonts_list', $fonts, now()->addDay());
            }
            return $fonts;
        } else {
            Toastr::error(_trans('Google Fonts API key not found'), 'Error');
            return [];
        }
    }
}
