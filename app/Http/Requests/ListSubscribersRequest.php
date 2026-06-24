<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ListSubscribersRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'category_uuid' => ['sometimes', 'uuid'],
            'email'         => ['sometimes', 'string'],
            'page'          => ['sometimes', 'integer', 'min:1'],
            'per_page'      => ['sometimes', 'integer', 'min:1', 'max:100'],
        ];
    }
}
