<?php

namespace App\Http\Requests;

use App\Rules\SlugRule;
use App\Http\Requests\Request;
use App\Models\Product;
use App\Rules\NumericCommaRule;
use Illuminate\Validation\Rule;
use App\Rules\ImagesProductRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Request as RequestHelper;

class ProductUpdateRequest extends FormRequest
{
    private $prepareData = [
        'images_json',
    ];

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
        $product = Product::find($this->id);

        $rules = [
            'name' => 'required|min:5|max:255',
            'sku' => [
                'required',
                Rule::unique('products')->where( function($query) {
                    return $query->where('seller_id', '=', request('seller_id'))->where('id', '!=', request('id'));
                }),
            ],
            'short_description' => 'required|min:5:max:150',
            'url_key' => [
                'required',
                Rule::unique('products')->where( function($query) {
                    return $query->where('id', '!=', request('id'));
                }),
                new SlugRule(),
            ],
            //'currency_id' => 'required',
            'price' => [ new NumericCommaRule() ],
            'width' => [ new NumericCommaRule() ],
            'height' => [ new NumericCommaRule() ],
            'depth' => [ new NumericCommaRule() ],
            'weight' => [ new NumericCommaRule() ],
            'seller_id' => 'required',
            'status' => 'required',
            'categories' => 'required',
            'images_json_validation.*.image' => new ImagesProductRule(1024, 1024, 700000),
            'date_of_rejected' => function ($attribute, $value, $fail) {
                $approvedStatus = RequestHelper::input('is_approved');
                if($approvedStatus === '0') {
                    if(empty($value)) return $fail('Debes indicar una fecha de rechazo');
                }
            },
            'rejected_reason' => function ($attribute, $value, $fail) {
                $approvedStatus = RequestHelper::input('is_approved');
                if($approvedStatus === '0') {
                    if(empty($value)) return $fail('Debes indicar una razon de rechazo');
                }
            },
            /* 'special_price_from' => function ($attribute, $value, $fail) {
                $specialPrice = RequestHelper::input('special_price');
                if( !($specialPrice == 0 || is_null($specialPrice)) ) {
                    if(is_null($value)) return $fail('Debes indicar una fecha de inicio para el precio de promoción');
                }
            }, */
            /* 'special_price_to' => function ($attribute, $value, $fail) {
                $specialPrice = RequestHelper::input('special_price');
                if( !($specialPrice == 0 || is_null($specialPrice)) ) {
                    if(is_null($value)) return $fail('Debes indicar una fecha de fin para el precio de promoción');
                }
            }, */
            'inventories_val' => function ($attribute, $value, $fail) {
                $fields = RequestHelper::all();
                foreach($fields as $param => $qty) {
                    $isAnInventory = substr($param, 0, 16) == 'inventory-source'; 
                    if($isAnInventory) {
                        if($qty < 0 ) {
                            return $fail('La cantidad en inventario no puede ser negativa');
                        } else if( is_null($qty) || $qty == "") {
                            return $fail('Debes indicar la cantidad en inventario');
                        }
                    }
                }
            }
        ];

        if ($product->is_housing ?? false) {
            $rules['housing_pricing'] = function ($attribute, $value, $fail) {
                $value = json_decode($value);

                foreach ($value as $dayPricing) {
                    if ($dayPricing->price_per_night == '') {
                        return $fail('El campo de precio por noche es requerido');
                    }
                }
            };
        }

        if ($product->is_tour ?? false) {
            $rules['tour_information'] = function ($attribute, $value, $fail) {
                $value = collect(json_decode($value));

                $attributes = [
                    'day' => 'Día', 
                    'adults_price' => 'Precio por adulto', 
                    'childrens_price' =>  'Precio por niño', 
                    'hour' => 'Hora de inicio',
                ];

                foreach ($attributes as $attribute => $text) {
                    $invalid = $value->pluck($attribute)->filter(function ($item) {
                        return $item === '';
                    });
    
                    if ($invalid->count()) {
                        return $fail("El campo de $text es requerido");
                    }
                }
            };
        }

        return $rules;
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
            'sku' => 'SKU',
            'description' => 'descripcion',
            'short_description' => 'resumen',
            'url_key' => 'URL Key',
            'currency_id' => 'moneda',
            'price' => 'precio',
            'width' => 'ancho',
            'height' => 'alto',
            'depth' => 'largo',
            'weight' => 'peso',
            'seller_id' => 'vendedor',
            'status' => 'estado',
            'categories' => 'categorias',
            'tour_date' => 'fecha y hora del tour',
            'adults_price' => 'precio por adulto',
            'childrens_price' => 'precio por niño',
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
            '*.required*' => 'Es necesario completar el campo :attribute.',
        ];
    }

    protected function prepareForValidation()
    {
        foreach ($this->prepareData as $attrName) {
            if (empty($this->$attrName)) {
                return;
            }

            $validation = json_decode($this->$attrName);
            $forValidation = [];

            foreach ($validation as $attrs) {
                $forValidation[] = (array) $attrs;
            }

            $this->merge([
                $attrName.'_validation' => $forValidation,
            ]);
        }
    }
}
