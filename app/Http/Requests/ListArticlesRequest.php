<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ListArticlesRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'category_uuid' => ['sometimes', 'uuid'],
            'distributed'   => ['sometimes', 'boolean'],
            'page'          => ['sometimes', 'integer', 'min:1'],
            'per_page'      => ['sometimes', 'integer', 'min:1', 'max:100'],
        ];
    }

    /**
     * Query strings send "true"/"false" as strings; Laravel's boolean rule only accepts 0/1.
     */
    protected function prepareForValidation(): void
    {
        if (!$this->has('distributed')) {
            return;
        }

        $parsed = filter_var($this->input('distributed'), FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE);

        if ($parsed !== null) {
            $this->merge(['distributed' => $parsed]);
        }
    }
}
