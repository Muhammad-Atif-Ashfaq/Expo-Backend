<?php

namespace App\Http\Requests\Api\Admin;

use Illuminate\Foundation\Http\FormRequest;
use App\Traits\ResponseTrait;

class ParticipientRequest extends FormRequest
{
    use ResponseTrait;

    public function rules(): array
    {
        return [
            'contest_id' => 'required|exists:contests,id',
            'fields_values' => 'required'
        ];
    }
}
