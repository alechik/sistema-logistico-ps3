<?php

namespace App\Http\Controllers;

use App\Models\Inventory;
use App\Models\InventoryTransaction;
use App\Models\TransactionType;
use App\Models\User;
use App\Models\Sale;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class InventoryTransactionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = InventoryTransaction::with([
            'inventory.product', 
            'inventory.warehouse', 
            'transactionType',
            'user',
            'sale'
        ]);

        // Filter by inventory_id
        if ($request->has('inventory_id')) {
            $query->where('inventory_id', $request->inventory_id);
        }

        // Filter by transaction_type_id
        if ($request->has('transaction_type_id')) {
            $query->where('transaction_type_id', $request->transaction_type_id);
        }

        // Filter by date range
        if ($request->has(['start_date', 'end_date'])) {
            $query->whereBetween('transaction_date', [
                $request->start_date,
                $request->end_date
            ]);
        }

        // Filter by user_id
        if ($request->has('user_id')) {
            $query->where('user_id', $request->user_id);
        }

        // Filter by sale_id
        if ($request->has('sale_id')) {
            $query->where('sale_id', $request->sale_id);
        }

        // Order by most recent first
        $transactions = $query->orderBy('transaction_date', 'desc')
            ->orderBy('id', 'desc')
            ->paginate($request->per_page ?? 20);

        return response()->json([
            'data' => $transactions->items(),
            'pagination' => [
                'total' => $transactions->total(),
                'per_page' => $transactions->perPage(),
                'current_page' => $transactions->currentPage(),
                'last_page' => $transactions->lastPage(),
                'from' => $transactions->firstItem(),
                'to' => $transactions->lastItem(),
            ]
        ], 200);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'inventory_id' => 'required|exists:inventories,id',
            'transaction_type_id' => 'required|exists:transaction_types,id',
            'quantity' => 'required|numeric|min:0.01',
            'unit_cost' => 'required|numeric|min:0',
            'total_cost' => 'required|numeric|min:0',
            'transaction_date' => 'required|date',
            'notes' => 'nullable|string|max:500',
            'reference' => 'nullable|string|max:100',
            'user_id' => 'nullable|exists:users,id',
            'sale_id' => 'nullable|exists:sales,id',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        // Get the transaction type to determine the effect on inventory
        $transactionType = TransactionType::findOrFail($request->transaction_type_id);
        $inventory = Inventory::findOrFail($request->inventory_id);

        return DB::transaction(function () use ($request, $transactionType, $inventory) {
            // Create the transaction
            $transaction = InventoryTransaction::create([
                'inventory_id' => $request->inventory_id,
                'transaction_type_id' => $request->transaction_type_id,
                'quantity' => $request->quantity,
                'unit_cost' => $request->unit_cost,
                'total_cost' => $request->total_cost,
                'transaction_date' => $request->transaction_date,
                'notes' => $request->notes,
                'reference' => $request->reference,
                'user_id' => $request->user_id ?? auth()->id(),
                'sale_id' => $request->sale_id,
            ]);

            // Update inventory based on transaction type
            if ($transactionType->effect === 'add') {
                $inventory->increment('quantity', $request->quantity);
            } elseif ($transactionType->effect === 'subtract') {
                // Check if there's enough quantity before subtracting
                if ($inventory->quantity < $request->quantity) {
                    DB::rollBack();
                    return response()->json([
                        'message' => 'No hay suficiente cantidad en el inventario para esta transacción.'
                    ], 422);
                }
                $inventory->decrement('quantity', $request->quantity);
            }

            // Update last_restock_date if this is a restock transaction
            if ($transactionType->name === 'Entrada' || $transactionType->effect === 'add') {
                $inventory->last_restock_date = now();
                $inventory->save();
            }

            return response()->json([
                'message' => 'Transacción registrada correctamente',
                'data' => $transaction->load(['inventory.product', 'inventory.warehouse', 'transactionType', 'user', 'sale'])
            ], 201);
        });
    }

    /**
     * Display the specified resource.
     */
    public function show(InventoryTransaction $inventoryTransaction)
    {
        return response()->json([
            'data' => $inventoryTransaction->load([
                'inventory.product', 
                'inventory.warehouse', 
                'transactionType',
                'user',
                'sale'
            ])
        ], 200);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, InventoryTransaction $inventoryTransaction)
    {
        // Prevent updates to transactions associated with sales
        if ($inventoryTransaction->sale_id) {
            return response()->json([
                'message' => 'No se pueden modificar transacciones asociadas a ventas.'
            ], 422);
        }

        $validator = Validator::make($request->all(), [
            'transaction_type_id' => 'sometimes|required|exists:transaction_types,id',
            'quantity' => 'sometimes|required|numeric|min:0.01',
            'unit_cost' => 'sometimes|required|numeric|min:0',
            'total_cost' => 'sometimes|required|numeric|min:0',
            'transaction_date' => 'sometimes|required|date',
            'notes' => 'nullable|string|max:500',
            'reference' => 'nullable|string|max:100',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        return DB::transaction(function () use ($request, $inventoryTransaction) {
            $originalQuantity = $inventoryTransaction->quantity;
            $originalTypeId = $inventoryTransaction->transaction_type_id;
            $inventory = $inventoryTransaction->inventory;
            
            // Get the transaction type (original or updated)
            $transactionType = TransactionType::find(
                $request->transaction_type_id ?? $originalTypeId
            );

            // If quantity or type changed, we need to adjust the inventory
            if ($request->has('quantity') || $request->has('transaction_type_id')) {
                // First, reverse the original transaction
                $this->reverseTransactionEffect($inventoryTransaction, $inventory);
                
                // Then apply the new effect
                $newQuantity = $request->quantity ?? $originalQuantity;
                
                if ($transactionType->effect === 'add') {
                    $inventory->increment('quantity', $newQuantity);
                } elseif ($transactionType->effect === 'subtract') {
                    // Check if there's enough quantity before subtracting
                    if ($inventory->quantity < $newQuantity) {
                        // If not enough, reverse the reversal and return error
                        $this->applyTransactionEffect($inventoryTransaction, $inventory);
                        DB::rollBack();
                        
                        return response()->json([
                            'message' => 'No hay suficiente cantidad en el inventario para esta transacción.'
                        ], 422);
                    }
                    $inventory->decrement('quantity', $newQuantity);
                }
            }

            // Update the transaction
            $inventoryTransaction->update($request->all());

            return response()->json([
                'message' => 'Transacción actualizada correctamente',
                'data' => $inventoryTransaction->fresh(['inventory.product', 'inventory.warehouse', 'transactionType', 'user'])
            ], 200);
        });
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(InventoryTransaction $inventoryTransaction)
    {
        // Prevent deletion of transactions associated with sales
        if ($inventoryTransaction->sale_id) {
            return response()->json([
                'message' => 'No se pueden eliminar transacciones asociadas a ventas.'
            ], 422);
        }

        return DB::transaction(function () use ($inventoryTransaction) {
            $inventory = $inventoryTransaction->inventory;
            
            // Reverse the transaction effect on inventory
            $this->reverseTransactionEffect($inventoryTransaction, $inventory);
            
            // Delete the transaction
            $inventoryTransaction->delete();

            return response()->json([
                'message' => 'Transacción eliminada correctamente'
            ], 200);
        });
    }

    /**
     * Reverse the effect of a transaction on inventory
     */
    private function reverseTransactionEffect($transaction, $inventory)
    {
        $transactionType = $transaction->transactionType;
        
        if ($transactionType->effect === 'add') {
            $inventory->decrement('quantity', $transaction->quantity);
        } elseif ($transactionType->effect === 'subtract') {
            $inventory->increment('quantity', $transaction->quantity);
        }
    }

    /**
     * Apply the effect of a transaction on inventory
     */
    private function applyTransactionEffect($transaction, $inventory)
    {
        $transactionType = $transaction->transactionType;
        
        if ($transactionType->effect === 'add') {
            $inventory->increment('quantity', $transaction->quantity);
        } elseif ($transactionType->effect === 'subtract') {
            $inventory->decrement('quantity', $transaction->quantity);
        }
    }

    /**
     * Get transaction summary by date range
     */
    public function getSummary(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'inventory_id' => 'nullable|exists:inventories,id',
            'transaction_type_id' => 'nullable|exists:transaction_types,id',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $query = InventoryTransaction::query()
            ->select(
                'transaction_type_id',
                DB::raw('COUNT(*) as transaction_count'),
                DB::raw('SUM(quantity) as total_quantity'),
                DB::raw('SUM(total_cost) as total_amount')
            )
            ->whereBetween('transaction_date', [$request->start_date, $request->end_date])
            ->groupBy('transaction_type_id')
            ->with('transactionType');

        if ($request->has('inventory_id')) {
            $query->where('inventory_id', $request->inventory_id);
        }

        if ($request->has('transaction_type_id')) {
            $query->where('transaction_type_id', $request->transaction_type_id);
        }

        $summary = $query->get();

        return response()->json(['data' => $summary], 200);
    }
}
