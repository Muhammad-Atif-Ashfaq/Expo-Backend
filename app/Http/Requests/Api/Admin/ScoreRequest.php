<?php

namespace App\Http\Requests\Api\Admin;

use Illuminate\Foundation\Http\FormRequest;
use App\Traits\ResponseTrait;

class ScoreRequest extends FormRequest
{
    use ResponseTrait;

    public function rules(): array
    {
        return [
            'judge_id' => 'required|exists:users,id',
            'participant_id' => 'required|exists:participients,id',
            'contest_id' => 'required|exists:contests,id',
            'scores' => 'required|array',
            'scores.*.field_name' => 'required|string',
            'scores.*.score' => 'required|numeric',
        ];
    }
}
