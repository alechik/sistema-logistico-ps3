<?php

namespace App\Http\Controllers;

use App\Models\WarehouseType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class WarehouseTypeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $warehouseTypes = WarehouseType::all();
        return response()->json(['data' => $warehouseTypes], 200);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255|unique:warehouse_types',
            'description' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $warehouseType = WarehouseType::create([
            'name' => $request->name,
            'description' => $request->description,
        ]);

        return response()->json(['data' => $warehouseType], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(WarehouseType $warehouseType)
    {
        return response()->json(['data' => $warehouseType], 200);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, WarehouseType $warehouseType)
    {
        $validator = Validator::make($request->all(), [
            'name' => [
                'sometimes',
                'required',
                'string',
                'max:255',
                Rule::unique('warehouse_types')->ignore($warehouseType->id),
            ],
            'description' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        // Prevent updating default warehouse types
        $defaultTypes = ['Principal', 'Secundario', 'Temporal', 'Externo'];
        if (in_array($warehouseType->name, $defaultTypes)) {
            return response()->json([
                'message' => 'No se pueden modificar los tipos de almacén predeterminados.'
            ], 422);
        }

        $warehouseType->update($request->all());

        return response()->json(['data' => $warehouseType], 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(WarehouseType $warehouseType)
    {
        // Prevent deleting default warehouse types
        $defaultTypes = ['Principal', 'Secundario', 'Temporal', 'Externo'];
        if (in_array($warehouseType->name, $defaultTypes)) {
            return response()->json([
                'message' => 'No se pueden eliminar los tipos de almacén predeterminados.'
            ], 422);
        }

        // Check if any warehouse is using this type
        if ($warehouseType->warehouses()->exists()) {
            return response()->json([
                'message' => 'No se puede eliminar el tipo de almacén porque hay almacenes asignados a él.'
            ], 422);
        }

        $warehouseType->delete();
        return response()->json(['message' => 'Tipo de almacén eliminado correctamente'], 200);
    }
}
