<?php

namespace App\Http\Requests\Search;

use Illuminate\Foundation\Http\FormRequest;

class RouteSearchRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'search'     => 'nullable|string|max:100',
            'route_type' => 'nullable|string|in:land,sea,combined',
            'is_master'  => 'nullable|in:0,1,true,false',
            'source_api' => 'nullable|string|in:OpenRouteService,OSRM,Searoute',
            'per_page'   => 'nullable|integer|min:5|max:100',
            'page'       => 'nullable|integer|min:1',
        ];
    }
}
