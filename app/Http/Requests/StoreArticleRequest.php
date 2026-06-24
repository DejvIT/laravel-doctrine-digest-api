<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreArticleRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'title'         => ['required', 'string', 'max:500'],
            'content'       => ['required', 'string'],
            'category_uuid' => ['required', 'uuid'],
        ];
    }
}
