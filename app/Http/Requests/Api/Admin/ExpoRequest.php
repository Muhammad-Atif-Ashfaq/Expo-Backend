<?php

namespace App\Http\Requests\Api\Admin;

use Illuminate\Foundation\Http\FormRequest;
use App\Traits\ResponseTrait;

class ExpoRequest extends FormRequest
{
    use ResponseTrait;

    public function rules(): array
    {
        return [
            'name' => 'required', 'string', 'max:255',
        ];
    }
}
