<?php

namespace App\Http\Controllers;

use App\Models\Inventory;
use App\Models\Product;
use App\Models\Warehouse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class InventoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        // Si es una petición API, devolver JSON
        if ($request->expectsJson() || $request->is('api/*')) {
            $inventories = Inventory::with(['product', 'warehouse'])->get();
            return response()->json(['data' => $inventories], 200);
        }
        
        // Si es una petición web, devolver vista
        return view('inventories.index');
    }
    
    /**
     * Get low stock inventory items (vista)
     */
    public function lowStock(Request $request)
    {
        // Si es una petición API, devolver JSON
        if ($request->expectsJson() || $request->is('api/*')) {
            $lowStock = Inventory::whereRaw('quantity <= minimum_stock')
                ->with(['product', 'warehouse'])
                ->get();
            return response()->json(['data' => $lowStock], 200);
        }
        
        // Si es una petición web, devolver vista
        $lowStockItems = Inventory::whereRaw('quantity <= minimum_stock')
            ->with(['product', 'warehouse'])
            ->get();
        return view('inventories.low-stock', compact('lowStockItems'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'product_id' => 'required|exists:products,id',
            'warehouse_id' => 'required|exists:warehouses,id',
            'quantity' => 'required|integer|min:0',
            'unit_cost' => 'required|numeric|min:0',
            'total_cost' => 'required|numeric|min:0',
            'minimum_stock' => 'required|integer|min:0',
            'maximum_stock' => 'required|integer|min:0|gt:minimum_stock',
            'location' => 'nullable|string|max:100',
            'last_restock_date' => 'nullable|date',
        ]);

        // Check if inventory already exists for this product and warehouse
        $existingInventory = Inventory::where('product_id', $request->product_id)
            ->where('warehouse_id', $request->warehouse_id)
            ->first();

        if ($existingInventory) {
            return response()->json([
                'message' => 'Ya existe un registro de inventario para este producto en este almacén.'
            ], 422);
        }

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $inventory = Inventory::create([
            'product_id' => $request->product_id,
            'warehouse_id' => $request->warehouse_id,
            'quantity' => $request->quantity,
            'unit_cost' => $request->unit_cost,
            'total_cost' => $request->total_cost,
            'minimum_stock' => $request->minimum_stock,
            'maximum_stock' => $request->maximum_stock,
            'location' => $request->location,
            'last_restock_date' => $request->last_restock_date,
        ]);

        return response()->json(['data' => $inventory->load(['product', 'warehouse'])], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Inventory $inventory)
    {
        return response()->json(['data' => $inventory->load(['product', 'warehouse'])], 200);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Inventory $inventory)
    {
        $validator = Validator::make($request->all(), [
            'product_id' => [
                'sometimes',
                'required',
                'exists:products,id',
                Rule::unique('inventories')
                    ->where('warehouse_id', $request->warehouse_id ?? $inventory->warehouse_id)
                    ->ignore($inventory->id)
            ],
            'warehouse_id' => [
                'sometimes',
                'required',
                'exists:warehouses,id',
                Rule::unique('inventories')
                    ->where('product_id', $request->product_id ?? $inventory->product_id)
                    ->ignore($inventory->id)
            ],
            'quantity' => 'sometimes|required|integer|min:0',
            'unit_cost' => 'sometimes|required|numeric|min:0',
            'total_cost' => 'sometimes|required|numeric|min:0',
            'minimum_stock' => 'sometimes|required|integer|min:0',
            'maximum_stock' => 'sometimes|required|integer|min:0|gt:minimum_stock',
            'location' => 'nullable|string|max:100',
            'last_restock_date' => 'nullable|date',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $inventory->update($request->all());

        return response()->json(['data' => $inventory->load(['product', 'warehouse'])], 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Inventory $inventory)
    {
        // Check if there are any transactions for this inventory
        if ($inventory->transactions()->exists()) {
            return response()->json([
                'message' => 'No se puede eliminar el registro de inventario porque tiene transacciones asociadas.'
            ], 422);
        }

        $inventory->delete();
        return response()->json(['message' => 'Registro de inventario eliminado correctamente'], 200);
    }

    /**
     * Get inventory by product and warehouse
     */
    public function getByProductAndWarehouse($productId, $warehouseId)
    {
        $inventory = Inventory::where('product_id', $productId)
            ->where('warehouse_id', $warehouseId)
            ->with(['product', 'warehouse'])
            ->first();

        if (!$inventory) {
            return response()->json([
                'message' => 'No se encontró inventario para el producto y almacén especificados.'
            ], 404);
        }

        return response()->json(['data' => $inventory], 200);
    }

    /**
     * Get low stock inventory items
     */
    public function getLowStock()
    {
        $lowStock = Inventory::whereRaw('quantity <= minimum_stock')
            ->with(['product', 'warehouse'])
            ->get();
            
        return response()->json(['data' => $lowStock], 200);
    }

    /**
     * Update stock (add or remove)
     */
    public function updateStock(Request $request, Inventory $inventory)
    {
        $validator = Validator::make($request->all(), [
            'quantity' => 'required|integer|min:0',
            'notes' => 'nullable|string|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $validated = $validator->validated();
        
        $inventory->update([
            'quantity' => $validated['quantity'],
            'last_restock_date' => now(),
        ]);

        // Create inventory transaction
        $transaction = $inventory->transactions()->create([
            'transaction_type_id' => 1, // Ajusta según tus tipos de transacción
            'quantity' => $validated['quantity'] - $inventory->quantity,
            'unit_cost' => $inventory->unit_cost,
            'total_cost' => $inventory->unit_cost * ($validated['quantity'] - $inventory->quantity),
            'transaction_date' => now(),
            'notes' => $validated['notes'] ?? 'Ajuste de inventario',
            'user_id' => auth()->id(),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Stock actualizado correctamente',
            'data' => [
                'inventory' => $inventory->load('product', 'warehouse'),
                'transaction' => $transaction
            ]
        ]);
    }

}
