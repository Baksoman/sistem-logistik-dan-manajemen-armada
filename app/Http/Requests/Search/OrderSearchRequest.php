<?php

namespace App\Http\Requests\Search;

use Illuminate\Foundation\Http\FormRequest;

class OrderSearchRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'search'       => 'nullable|string|max:100',
            'status'       => 'nullable|string|in:Draft,Pending Approval,Confirmed,Assigned,Arrived at Hub,Completed,Cancelled',
            'warehouse_id' => 'nullable|uuid|exists:warehouses,id',
            'customer_id'  => 'nullable|uuid|exists:customers,id',
            'created_from' => 'nullable|date',
            'created_to'   => 'nullable|date|after_or_equal:created_from',
            'per_page'     => 'nullable|integer|min:5|max:100',
            'page'         => 'nullable|integer|min:1',
        ];
    }
}
