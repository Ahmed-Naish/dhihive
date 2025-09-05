<?php

namespace Modules\Saas\Http\Requests;

use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Request;
use Illuminate\Foundation\Http\FormRequest;

class CheckoutStoreRequest extends FormRequest
{
   /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        if (isRecaptchaEnable() && request('source') != 'Admin') {
            loadRecaptcha();
        }

        $isRecaptchaRequired = isRecaptchaEnable() ? true : false;

        return [
            'pricing_plan_price_id' => 'required',
            'expiry_date'           => 'required|date',
            'name'                  => 'required|max:255',
            'company_name'          => 'required|max:255|unique:companies,company_name',
            'phone'                 => [
                'required', 'max:255',
                Rule::unique('companies', 'phone'),
                Rule::unique('users', 'phone'),
                'regex:/^(?:\+|\d{1,4})[ -]?(?:\(\d{1,}\)[ -]?)?\d+(?:[ -]?\d+)*$/',
            ],
            'email'                 => [
                'required', 'email', 'max:255',
                Rule::unique('companies', 'email'),
                Rule::unique('users', 'email'),
            ],
            'trade_licence_number'  => 'required|max:255|unique:companies,trade_licence_number',
            'subdomain'             => [
                                        'required',
                                        'max:255',
                                        Rule::unique('companies', 'subdomain')->where(function ($query) {
                                            $currentSubdomain = request('subdomain');
                                            $appDomain = @base_settings('company_domain');
                                    
                                            return $query->where('subdomain', $currentSubdomain)
                                                ->orWhere('subdomain', $currentSubdomain . '.' . $appDomain);
                                        }),
                                        'regex:/^[a-zA-Z0-9-]+$/',
                                    ],
            'total_employee'        => 'required',
            'business_type'         => 'required|max:255',
            'country_id'            => 'required',
            'payment_gateway'       => 'required',
            'g-recaptcha-response'  => [Rule::requiredIf($isRecaptchaRequired)]
        ];
    }


    public function messages()
    {
        return [
            'pricing_plan_price_id.required'    => _trans('validation.Pricing Plan is required'),
            'expiry_date.required'              => _trans('validation.Expiry Date is required'),
            'expiry_date.date'                  => _trans('validation.Expiry Date must be a date'),
            'name.required'                     => _trans('validation.Name is required'),
            'name.max'                          => _trans('validation.Name may not be greater than 255 characters'),
            'company_name.required'             => _trans('validation.Company Name is required'),
            'company_name.max'                  => _trans('validation.Company Name may not be greater than 255 characters'),
            'company_name.unique'               => _trans('validation.Company Name already exists'),
            'phone.required'                    => _trans('validation.Phone is required'),
            'phone.max'                         => _trans('validation.Phone may not be greater than 255 characters'),
            'phone.unique'                      => _trans('validation.Phone already exists'),
            'phone.regex'                       => _trans('validation.Invalid phone number format') . ' ' . _trans('validation.Please enter a valid phone number'),
            'email.required'                    => _trans('validation.Email is required'),
            'email.email'                       => _trans('validation.Email must be an email'),
            'email.max'                         => _trans('validation.Email may not be greater than 255 characters'),
            'email.unique'                      => _trans('validation.Email already exists'),
            'trade_licence_number.required'     => _trans('validation.Trade Licence Number is required'),
            'trade_licence_number.max'          => _trans('validation.Trade Licence Number may not be greater than 255 characters'),
            'trade_licence_number.unique'       => _trans('validation.Trade Licence Number already exists'),
            'subdomain.required'                => _trans('validation.Subdomain is required'),
            'subdomain.max'                     => _trans('validation.Subdomain may not be greater than 255 characters'),
            'subdomain.unique'                  => _trans('validation.Subdomain already exists'),
            'subdomain.regex'                   => _trans('validation.Invalid subdomain format') . ' ' . _trans('validation.Only letters (both uppercase and lowercase), numbers, and hyphens are allowed'),
            'total_employee.required'           => _trans('validation.Total Employee is required'),
            'business_type.required'            => _trans('validation.Business Type is required'),
            'business_type.max'                 => _trans('validation.Business Type may not be greater than 255 characters'),
            'country_id.required'               => _trans('validation.Country is required'),
            'payment_gateway.required'          => _trans('validation.Payment Gateway is required'),
            'g-recaptcha-response.required'     => _trans('validation.Google Recaptcha is required'),
        ];
    }
}
