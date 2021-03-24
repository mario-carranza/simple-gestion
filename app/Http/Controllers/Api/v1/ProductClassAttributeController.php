<?php

namespace App\Http\Controllers\Api\v1;

use Exception;
use App\Models\ProductClass;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Models\ProductClassAttribute;
use App\Http\Controllers\Api\Controller;
use Illuminate\Support\Facades\Validator;
use App\Http\Resources\ProductClassAttributeResource;
use App\Http\Requests\Api\ProductClassAttributeRequest;

class ProductClassAttributeController extends Controller
{

    public function showByCode(Request $request)
    {
        $classAttribute = ProductClassAttribute::where('json_attributes->code', $request['code'])->first();

        if (!$classAttribute) return response()->json([ 
            'status' => 'error', 
            'message' => 'No existe ningun atributo con el codigo indicado'
        ],  404);

        
        return response()->json([
            'status' => 'success',
            'data' => new ProductClassAttributeResource($classAttribute),
        ], 200);
    }

    public function store(ProductClassAttributeRequest $request)
    {
        $user = Auth::user();
        $productClass = ProductClass::where('code', $request->class_code)->first();
        $options = $request->type_attribute === 'select' ? json_decode($request->options, true) : null;

        try {
            $attribute = ProductClassAttribute::create([
                'product_class_id' => $productClass->id,
                'json_options' => $options,
                'json_attributes' => [
                    'name' => $request->name,
                    'code' => $request->code,
                    'type_attribute' => $request->type_attribute,
                ],
                'company_id' => $user->companies->first()->id,
            ]);
        } catch (\Illuminate\Database\QueryException $exception) {
            return response()->json([ 'status' => 'error', 'message' => $exception ], 400);
        }

        return response()->json([
            'status' => 'success',
            'message' => 'Atributo creado exitosamente',
            'data' => new ProductClassAttributeResource($attribute),
        ], 200);
    }

    public function delete($code)
    {
        $attribute = ProductClassAttribute::where('json_attributes->code', $code)->first();

        if (!$attribute) return response()->json([ 
            'status' => 'error', 
            'message' => 'El codigo de lel atributo no existe'
        ],  404);

        DB::beginTransaction();

        try {
            $attribute->forceDelete();
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([ 
                'status' => 'error', 
                'message' => 'Ocurrio un error intentando eliminar el atributo.',
                'error_message' => $e->getMessage(),
            ],  400);
        }

        DB::commit();

        return response()->json([
            'status' => 'success',
            'message' => 'Atributo eliminado',
            'data' => $attribute,
        ], 200);
    }
}