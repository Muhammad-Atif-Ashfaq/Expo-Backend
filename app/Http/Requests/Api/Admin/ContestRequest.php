<?php

namespace App\Http\Requests\Api\Admin;

use Illuminate\Foundation\Http\FormRequest;
use App\Traits\ResponseTrait;

class ContestRequest extends FormRequest
{
    use ResponseTrait;

    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'expo_id' => 'required|exists:expos,id',
            'start_date_time' => 'required|date',
            'end_date_time' => 'required|date',
            'max_contestent' => 'nullable|integer|min:1',
        ];
    }
}