<?php

namespace Modules\EmployeeDocuments\Http\Requests;

use Illuminate\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;

class DocumentTypeRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        switch ($this->method()) {
            case 'GET':
            case 'DELETE':
                {
                    return [];
                }
            case 'POST':
                {
                    return [
                        'name' => [
                            'required',
                            'max:255',
                            Rule::unique('user_document_types', 'name')->where('company_id', auth()->user()->company_id)->ignore($this->id),
                        ]
                    ];
                }
            case 'PATCH':
                {
                    return [
                        'name' => [
                            'required',
                            'max:255',
                            Rule::unique('user_document_types', 'name')->where('company_id', auth()->user()->company_id)->ignore($this->id),
                        ]
                    ];
                }
            default:
                break;
        }
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
}
