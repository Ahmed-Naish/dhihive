<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use App\Helpers\CoreApp\Traits\ApiReturnFormatTrait;
use Illuminate\Http\Exceptions\HttpResponseException;

class RemarkApiRequest extends FormRequest
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
            'type' => ['required', 'in:break,attendance'], // Only allow 'break' or 'attendance'
            'id' => ['required', 'numeric'],
            'remark' => ['required', 'max:255'],
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

    /**
     * Get the error messages for the defined validation rules.
     *
     * @return array
     */
    public function messages()
    {
        return [
            'type.required'   => _trans('validation.The type field is required'),
            'type.in'         => _trans("validation.The type must be either 'break' or 'attendance'"),
            'id.required'     => _trans('validation.The ID field is required'),
            'id.numeric'      => _trans('validation.The ID must be a number'),
            'remark.required' => _trans('validation.The remark field is required'),
            'remark.max'      => _trans('validation.The remark may not be greater than 255 characters'),
        ];
    }

    /**
     * Handle a failed validation attempt.
     *
     * @param  \Illuminate\Contracts\Validation\Validator  $validator
     * @return void
     *
     * @throws \Illuminate\Http\Exceptions\HttpResponseException
     */
    public function failedValidation(Validator $validator)
    {
        throw new HttpResponseException($this->responseWithError(__('Validation Error'), $validator->errors()->all(), 422));
    }
}
