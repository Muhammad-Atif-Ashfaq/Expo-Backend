<?php

namespace App\Http\Requests\Api\Admin;

use Illuminate\Foundation\Http\FormRequest;
use App\Traits\ResponseTrait;

class FormFieldRequest extends FormRequest
{
    use ResponseTrait;

    public function rules(): array
    {
        return [
            'formData' => 'required|array',
            'formData.*.contest_id' => 'required|string|max:255',
            'formData.*.name' => 'required|string|max:255',
            'formData.*.type' => 'required|string|max:255',
            'formData.*.label' => 'required|string|max:255',
            'formData.*.required' => 'required',
            'formData.*.is_important' => 'required',
        ];
    }
}
