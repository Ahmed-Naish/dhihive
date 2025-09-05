<?php

namespace App\Http\Requests\Settings;

use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Auth;
use Illuminate\Foundation\Http\FormRequest;

class LanguageRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        $id        = $this->route('id');
        $companyId = Auth::user()->company_id;

        switch ($this->method()) {
            case 'GET':
            case 'DELETE': {
                    return [];
                }
            case 'POST':
            case 'PATCH': {
                    return [
                        'name' => [
                            'required',
                            Rule::unique('languages')->where(function ($query) use ($companyId, $id) {
                                return $query->where('company_id', $companyId)
                                    ->where('id', '!=', $id);
                            }),
                        ],
                        'status' => 'required',
                        'rtl'    => 'required',
                        'native' => 'required',
                        'code'   => [
                            'required',
                            Rule::unique('languages')->where(function ($query) use ($companyId, $id) {
                                return $query->where('company_id', $companyId)
                                    ->where('id', '!=', $id);
                            }),
                        ],
                    ];
                }
            default:
                break;
        }
    }
    public function messages()
    {
        return [
            'name.required'   => _trans('common.Name is required'),
            'status.required' => _trans('common.Status is required'),
            'rtl.required'    => _trans('common.RTL is required'),
            'native.required' => _trans('common.Native is required'),
            'code.required'   => _trans('common.Code is required'),
        ];
    }
}
