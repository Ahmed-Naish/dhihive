<?php

namespace Modules\Saas\Http\Requests;

use Illuminate\Support\Str;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use App\Helpers\CoreApp\Traits\ApiReturnFormatTrait;
use Illuminate\Http\Exceptions\HttpResponseException;

class UpgradePlanRequest extends FormRequest
{
    use ApiReturnFormatTrait;

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
        return [
            'pricing_plan_price_id'     => ['required'],
            'payment_gateway'           => ['required'],
            'stripeToken'               => ['required_if:payment_gateway,Stripe'],
            'offline_payment_details'   => ['required_if:payment_gateway,Offline Payment'],
            'expiry_date'               => ['required', 'date'],
            'source'                    => ['required'],
        ];
    }

    /**
     * Get the error messages json response for the defined validation rules.
     *
     * @return array
     */
    public function failedValidation(Validator $validator)
    {
        throw new HttpResponseException($this->responseWithError(_trans('message.Validation Error'), $validator->errors()));
    }
}
