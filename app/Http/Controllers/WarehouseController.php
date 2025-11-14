<?php

namespace App\Http\Controllers;

use App\Models\Warehouse;
use App\Models\WarehouseType;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class WarehouseController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        // Si es una petición API, devolver JSON
        if ($request->expectsJson() || $request->is('api/*')) {
            $warehouses = Warehouse::with(['warehouseType', 'manager'])->get();
            return response()->json(['data' => $warehouses], 200);
        }
        
        // Si es una petición web, devolver vista
        return view('warehouses.index');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:50|unique:warehouses',
            'description' => 'nullable|string',
            'address' => 'nullable|string',
            'city' => 'nullable|string|max:100',
            'state' => 'nullable|string|max:100',
            'country' => 'nullable|string|max:100',
            'postal_code' => 'nullable|string|max:20',
            'phone' => 'nullable|string|max:50',
            'email' => 'nullable|email|max:100',
            'warehouse_type_id' => 'required|exists:warehouse_types,id',
            'manager_id' => 'nullable|exists:users,id',
            'is_active' => 'boolean',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $warehouse = Warehouse::create([
            'name' => $request->name,
            'code' => $request->code,
            'description' => $request->description,
            'address' => $request->address,
            'city' => $request->city,
            'state' => $request->state,
            'country' => $request->country,
            'postal_code' => $request->postal_code,
            'phone' => $request->phone,
            'email' => $request->email,
            'warehouse_type_id' => $request->warehouse_type_id,
            'manager_id' => $request->manager_id,
            'is_active' => $request->has('is_active') ? $request->is_active : true,
        ]);

        return response()->json(['data' => $warehouse->load(['warehouseType', 'manager'])], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Warehouse $warehouse)
    {
        return response()->json(['data' => $warehouse->load(['warehouseType', 'manager'])], 200);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Warehouse $warehouse)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'sometimes|required|string|max:255',
            'code' => [
                'sometimes',
                'required',
                'string',
                'max:50',
                Rule::unique('warehouses')->ignore($warehouse->id),
            ],
            'description' => 'nullable|string',
            'address' => 'nullable|string',
            'city' => 'nullable|string|max:100',
            'state' => 'nullable|string|max:100',
            'country' => 'nullable|string|max:100',
            'postal_code' => 'nullable|string|max:20',
            'phone' => 'nullable|string|max:50',
            'email' => 'nullable|email|max:100',
            'warehouse_type_id' => 'sometimes|required|exists:warehouse_types,id',
            'manager_id' => 'nullable|exists:users,id',
            'is_active' => 'sometimes|boolean',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $warehouse->update($request->all());

        return response()->json(['data' => $warehouse->load(['warehouseType', 'manager'])], 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Warehouse $warehouse)
    {
        // Check if warehouse has inventory
        if ($warehouse->inventories()->exists()) {
            return response()->json([
                'message' => 'No se puede eliminar el almacén porque tiene inventario asociado.'
            ], 422);
        }

        // Check if warehouse has sales
        if ($warehouse->sales()->exists()) {
            return response()->json([
                'message' => 'No se puede eliminar el almacén porque tiene ventas asociadas.'
            ], 422);
        }

        $warehouse->delete();
        return response()->json(['message' => 'Almacén eliminado correctamente'], 200);
    }

    /**
     * Get warehouses by type
     */
    public function getByType($typeId)
    {
        $warehouses = Warehouse::where('warehouse_type_id', $typeId)
            ->where('is_active', true)
            ->get();
            
        return response()->json(['data' => $warehouses], 200);
    }
}
