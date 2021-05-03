<?php

namespace App\Http\Requests;

use App\Rules\SlugRule;
use App\Http\Requests\Request;
use Illuminate\Validation\Rule;
use App\Rules\ImageDimensionCategoryRule;
use Illuminate\Foundation\Http\FormRequest;

class ProductCategoryRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        // only allow updates if the user is logged in
        return backpack_auth()->check();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'name' => 'required|min:1|max:255',
            'code' => [
                'required', 
                'min:1',
                'max:255',
                Rule::unique('product_categories')->ignore($this->id),
            ],
            'position' => 'required|numeric|min:0',
            'display_mode' => [
                'required',
                Rule::in(['products_and_description']),
            ],
            /* 'icon' => 'required', */
            'slug' => [
                'required',
                Rule::unique('product_categories')->where( function($query) {
                    return $query->where('id', '!=', request('id'));
                }),
                new SlugRule(),
            ]
        ];
    }

    /**
     * Get the validation attributes that apply to the request.
     *
     * @return array
     */
    public function attributes()
    {
        return [
            'name' => 'nombre',
            'code' => 'codigo',
            'display_mode' => 'modo de visualizacion',
            'icon' => 'icono',
            'parent_id' => 'categoria padre',
            'position' => 'posición'
        ];
    }

    /**
     * Get the validation messages that apply to the request.
     *
     * @return array
     */
    public function messages()
    {
        return [
            //
        ];
    }
}
