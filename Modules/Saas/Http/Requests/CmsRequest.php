<?php

namespace Modules\Saas\Http\Requests;

use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Request;
use Illuminate\Foundation\Http\FormRequest;

class CmsRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        $route = Request::route()->getName();
        if ($route == 'saas.cms.store') {
            return [
                'title'         => 'required|max:255',
                'description'   => 'required',
                'image'         => 'nullable|mimes:jpeg,png,jpg,gif|max:2048',
                'link'          => 'nullable|max:255',
                'text_color'    => 'required|max:255',
                'bg_color'      => 'required|max:255',
                'order'         => 'required|numeric|unique:saas_cms,order',
                'style'         => 'required',
                'status'        => 'required',
            ];
        }
        if ($route == 'saas.cms.update') {
            return [
                'title'         => 'required|max:255',
                'description'   => 'required',
                'image'         => 'nullable|mimes:jpeg,png,jpg,gif|max:2048',
                'link'          => 'nullable|max:255',
                'text_color'    => 'required|max:255',
                'bg_color'      => 'required|max:255',
                'order'         => 'required|numeric|unique:saas_cms,order,'. $this->id,
                'style'         => 'required',
                'status'        => 'required',
            ];
        }
        
    }

    public function messages()
    {
        return [
            'title.required'                  => _trans('validation.Title is required'),
            'title.max'                       => _trans('common.Title may not be greater than 255 characters'),

            'description.required'            => _trans('validation.Description is required'),
            'description.max'                 => _trans('common.Description may not be greater than 255 characters'),

            // 'image.required'                  => _trans('validation.Image is required'),
            'image.mimes'                     => _trans('validation.Image must be a file of type: jpeg, png, jpg, gif.'),
            'image.max'                       => _trans('validation.Image should not more than 2048KB.'),

            // 'link.required'                   => _trans('validation.Link is required'),
            'link.max'                        => _trans('common.Link may not be greater than 255 characters'),

            'text_color.required'             => _trans('validation.Text Color is required'),
            'text_color.max'                  => _trans('common.Text Color may not be greater than 255 characters'),

            'bg_color.required'               => _trans('validation.Background color is required'),
            'bg_color.max'                    => _trans('common.Background color may not be greater than 255 characters'),

            'order.required'                  => _trans('validation.Order is required'),
            'order.numeric'                   => _trans('validation.Order should be a numeric number'),
            'order.unique'                    => _trans('validation.Order exists'),

            'style.required'                  => _trans('validation.Style is required'),

            'status.required'                 => _trans('validation.Status is required'),
        ];
    }
}
