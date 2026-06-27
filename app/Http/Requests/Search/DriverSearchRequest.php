<?php

namespace App\Http\Requests\Search;

use Illuminate\Foundation\Http\FormRequest;

class DriverSearchRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'search'         => 'nullable|string|max:100',
            'status'         => 'nullable|string|in:available,on_trip,inactive',
            'license_type'   => 'nullable|string|in:A,B1,B2',
            'license_status' => 'nullable|string|in:expired,expiring',
            'per_page'       => 'nullable|integer|min:5|max:100',
            'page'           => 'nullable|integer|min:1',
        ];
    }
}
