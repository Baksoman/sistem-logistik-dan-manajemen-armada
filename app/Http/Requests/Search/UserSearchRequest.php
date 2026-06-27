<?php

namespace App\Http\Requests\Search;

use Illuminate\Foundation\Http\FormRequest;

class UserSearchRequest extends FormRequest
{
    public function authorize(): bool
    {
        // Authorization is handled at the Policy level in the controller.
        return true;
    }

    public function rules(): array
    {
        return [
            'search'         => 'nullable|string|max:100',
            'role'           => 'nullable|string|max:100',
            'is_active'      => 'nullable|in:0,1,true,false',
            'email_verified' => 'nullable|in:verified,unverified',
            'per_page'       => 'nullable|integer|min:5|max:100',
            'page'           => 'nullable|integer|min:1',
        ];
    }
}
