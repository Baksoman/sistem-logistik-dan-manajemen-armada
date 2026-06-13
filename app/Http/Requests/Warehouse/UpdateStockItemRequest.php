<?php

namespace App\Http\Requests\Warehouse;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateStockItemRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'warehouse_id' => ['required', 'exists:warehouses,id'],
            'category_id' => ['required', 'exists:item_categories,id'],
            'unit_type_id' => ['required', 'exists:unit_types,id'],
            'sku' => ['required', 'string', 'max:255', Rule::unique('stock_items')->ignore($this->route('inventory'))],
            'gtin' => ['nullable', 'string', 'max:255'],
            'name' => ['required', 'string', 'max:255'],
            'quantity' => ['required', 'numeric', 'min:0'],
            'min_quantity' => ['required', 'numeric', 'min:0'],
            'weight_kg' => ['required', 'numeric', 'min:0'],
            'volume_cbm' => ['required', 'numeric', 'min:0'],
            'zone' => ['nullable', 'string', 'max:255'],
            'bin_location' => ['nullable', 'string', 'max:255']
        ];
    }
}
