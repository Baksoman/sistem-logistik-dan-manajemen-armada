<?php

namespace App\Http\Requests\Search;

use Illuminate\Foundation\Http\FormRequest;

class InventorySearchRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'search' => ['nullable', 'string', 'max:255'],
            'warehouse_id' => ['nullable', 'string'],
            'category_id' => ['nullable', 'string'],
            'is_low_stock' => ['nullable', 'boolean'],
            'date_from' => ['nullable', 'date'],
            'date_to' => ['nullable', 'date', 'after_or_equal:date_from'],
            'per_page' => ['nullable', 'integer', 'min:1', 'max:100'],
        ];
    }
}
