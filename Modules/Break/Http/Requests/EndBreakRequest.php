<?php

namespace Modules\Break\Http\Requests;

use Illuminate\Validation\Rule;
use Modules\Break\Entities\UserBreak;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use App\Helpers\CoreApp\Traits\ApiReturnFormatTrait;
use Illuminate\Http\Exceptions\HttpResponseException;

class EndBreakRequest extends FormRequest
{
    use ApiReturnFormatTrait;
    
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $break = UserBreak::with('breakType')->where('id', $this->break_id)->first();
        
        return [
           'end_time' => ['required', 'date_format:H:i:s'],
           'remark' => [
                Rule::requiredIf(function () use ($break) {
                    return $break->breakType->is_remark_required ? true : false;
                })
           ]
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
            'end_time.required' => _trans('common.End time is required'),
            'end_time.date_format' => _trans('The end time must be in the 24-hour format (H:i:s)'),
            'remark.required' => _trans('A remark is required for this break'),
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
