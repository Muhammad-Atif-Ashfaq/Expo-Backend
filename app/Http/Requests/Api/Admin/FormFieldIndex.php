<?php

namespace App\Http\Requests\Api\Admin;

use Illuminate\Foundation\Http\FormRequest;
use App\Traits\ResponseTrait;

class FormFieldIndex extends FormRequest
{
    use ResponseTrait;

    public function rules(): array
    {
        return [
            'contest_id' => 'required|exists:contests,id',
        ];
    }
}
