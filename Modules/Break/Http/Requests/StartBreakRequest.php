<?php

namespace Modules\Break\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use App\Helpers\CoreApp\Traits\ApiReturnFormatTrait;
use Illuminate\Http\Exceptions\HttpResponseException;

class StartBreakRequest extends FormRequest
{
    use ApiReturnFormatTrait;
    
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
           'start_time' => ['required', 'date_format:H:i:s'],
        ];
    }

    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    public function messages()
    {
        return [
            'start_time.required' => _trans('common.Start time is required'),
            'start_time.date_format' => _trans('The start time must be in the 24-hour format (H:i:s)'),
        ];
    }

    /**
     * Get the error messages json response for the defined validation rules.
     *
     * @return array
     */
    public function failedValidation(Validator $validator)
    {
        throw new HttpResponseException($this->responseWithError(__('Validation Error'), $validator->errors()->all(), 422));
    }
}
