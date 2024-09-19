<?php
namespace App\Http\Requests\Api\Admin;

use Illuminate\Foundation\Http\FormRequest;
use App\Traits\ResponseTrait;

class JudgeRequest extends FormRequest
{
    use ResponseTrait;

    public function rules(): array
    {
        return [
            'contest_id' => 'required|exists:contests,id',
            'judge_name.*' => 'required',
            'email.*' => 'required',
            'phone.*' => 'nullable|string',
            'fields.*.name' => 'required|string|max:255',
            'fields.*.label' => 'required|string|max:255',
            'fields.*.type' => 'required|string|max:255',
            'fields.*.required' => 'required|boolean',
            'profile_picture.*' => 'nullable'
        ];
    }

    public function authorize(): bool
    {
        return true;
    }
}
