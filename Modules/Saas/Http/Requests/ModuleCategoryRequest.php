<?php

namespace Modules\Saas\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Request;

class ModuleCategoryRequest extends FormRequest
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
        $route = Request::route()->getName();
        if ($route == 'saas.subscription_module_categories.store') {
            return [
                'title' => 'required',
            ];
        }
        if ($route == 'saas.subscription_module_categories.update') {
            return [
                'title' => 'required|max:255',
            ];
        }

    }

    public function messages()
    {
        return [
            'title.required' => _trans('validation.Title is required'),
        ];
    }
}
