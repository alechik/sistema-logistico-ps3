<?php

namespace App\Http\Controllers;

use App\Models\Sale;
use App\Models\SaleItem;
use App\Models\Inventory;
use App\Models\InventoryTransaction;
use App\Models\TransactionType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class SaleItemController extends Controller
{
    /**
     * Display a listing of the resource for a specific sale.
     */
    public function index($saleId)
    {
        $items = SaleItem::with('product')
            ->where('sale_id', $saleId)
            ->get();
            
        return response()->json(['data' => $items], 200);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request, $saleId)
    {
        $sale = Sale::findOrFail($saleId);

        // Prevent adding items to completed or cancelled sales
        if (in_array($sale->status, ['completed', 'cancelled'])) {
            return response()->json([
                'message' => 'No se pueden agregar ítems a una venta ' . $sale->status
            ], 422);
        }

        $validator = Validator::make($request->all(), [
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|integer|min:1',
            'unit_price' => 'required|numeric|min:0',
            'subtotal' => 'required|numeric|min:0',
            'tax' => 'required|numeric|min:0',
            'discount' => 'required|numeric|min:0',
            'total' => 'required|numeric|min:0',
            'notes' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        // Check inventory availability
        $inventory = Inventory::where('product_id', $request->product_id)
            ->where('warehouse_id', $sale->warehouse_id)
            ->first();

        if (!$inventory || $inventory->quantity < $request->quantity) {
            return response()->json([
                'message' => 'No hay suficiente stock disponible para este producto.'
            ], 422);
        }

        return DB::transaction(function () use ($request, $sale, $inventory) {
            // Create sale item
            $saleItem = $sale->items()->create([
                'product_id' => $request->product_id,
                'quantity' => $request->quantity,
                'unit_price' => $request->unit_price,
                'subtotal' => $request->subtotal,
                'tax' => $request->tax,
                'discount' => $request->discount,
                'total' => $request->total,
                'notes' => $request->notes,
            ]);

            // Update inventory
            $inventory->decrement('quantity', $request->quantity);
            $inventory->save();

            // Create inventory transaction
            $transactionType = TransactionType::where('name', 'Salida')->first();
            
            $inventory->transactions()->create([
                'transaction_type_id' => $transactionType->id,
                'quantity' => $request->quantity,
                'unit_cost' => $request->unit_price,
                'total_cost' => $request->total,
                'transaction_date' => now(),
                'notes' => 'Venta #' . $sale->invoice_number . ' - Item agregado',
                'user_id' => auth()->id(),
                'sale_id' => $sale->id,
            ]);

            // Update sale totals
            $this->updateSaleTotals($sale);

            return response()->json([
                'message' => 'Ítem agregado correctamente',
                'data' => $saleItem->load('product')
            ], 201);
        });
    }

    /**
     * Display the specified resource.
     */
    public function show($saleId, $itemId)
    {
        $saleItem = SaleItem::with('product')
            ->where('sale_id', $saleId)
            ->findOrFail($itemId);
            
        return response()->json(['data' => $saleItem], 200);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $saleId, $itemId)
    {
        $sale = Sale::findOrFail($saleId);
        $saleItem = $sale->items()->findOrFail($itemId);

        // Prevent updating items in completed or cancelled sales
        if (in_array($sale->status, ['completed', 'cancelled'])) {
            return response()->json([
                'message' => 'No se pueden modificar ítems en una venta ' . $sale->status
            ], 422);
        }

        $validator = Validator::make($request->all(), [
            'quantity' => 'sometimes|required|integer|min:1',
            'unit_price' => 'sometimes|required|numeric|min:0',
            'subtotal' => 'sometimes|required|numeric|min:0',
            'tax' => 'sometimes|required|numeric|min:0',
            'discount' => 'sometimes|required|numeric|min:0',
            'total' => 'sometimes|required|numeric|min:0',
            'notes' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        return DB::transaction(function () use ($request, $sale, $saleItem) {
            $oldQuantity = $saleItem->quantity;
            $newQuantity = $request->has('quantity') ? $request->quantity : $oldQuantity;
            $quantityDifference = $newQuantity - $oldQuantity;

            // If quantity changed, update inventory
            if ($quantityDifference != 0) {
                $inventory = Inventory::where('product_id', $saleItem->product_id)
                    ->where('warehouse_id', $sale->warehouse_id)
                    ->first();

                if (!$inventory || $inventory->quantity < $quantityDifference) {
                    return response()->json([
                        'message' => 'No hay suficiente stock disponible para esta actualización.'
                    ], 422);
                }

                // Update inventory
                $inventory->decrement('quantity', $quantityDifference);
                $inventory->save();

                // Create inventory transaction for the difference
                $transactionType = TransactionType::where('name', 'Ajuste de inventario')->first();
                
                $inventory->transactions()->create([
                    'transaction_type_id' => $transactionType->id,
                    'quantity' => abs($quantityDifference),
                    'unit_cost' => $saleItem->unit_price,
                    'total_cost' => abs($quantityDifference * $saleItem->unit_price),
                    'transaction_date' => now(),
                    'notes' => $quantityDifference > 0 
                        ? 'Ajuste por modificación de cantidad en venta #' . $sale->invoice_number
                        : 'Devolución por reducción de cantidad en venta #' . $sale->invoice_number,
                    'user_id' => auth()->id(),
                    'sale_id' => $sale->id,
                ]);
            }

            // Update sale item
            $saleItem->update($request->all());

            // Update sale totals
            $this->updateSaleTotals($sale);

            return response()->json([
                'message' => 'Ítem actualizado correctamente',
                'data' => $saleItem->fresh('product')
            ], 200);
        });
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($saleId, $itemId)
    {
        $sale = Sale::findOrFail($saleId);
        $saleItem = $sale->items()->findOrFail($itemId);

        // Prevent deleting items from completed or cancelled sales
        if (in_array($sale->status, ['completed', 'cancelled'])) {
            return response()->json([
                'message' => 'No se pueden eliminar ítems de una venta ' . $sale->status
            ], 422);
        }

        return DB::transaction(function () use ($sale, $saleItem) {
            // Return item quantity to inventory
            $inventory = Inventory::where('product_id', $saleItem->product_id)
                ->where('warehouse_id', $sale->warehouse_id)
                ->first();

            if ($inventory) {
                $inventory->increment('quantity', $saleItem->quantity);
                $inventory->save();

                // Create inventory transaction for the return
                $transactionType = TransactionType::where('name', 'Ajuste de inventario')->first();
                
                $inventory->transactions()->create([
                    'transaction_type_id' => $transactionType->id,
                    'quantity' => $saleItem->quantity,
                    'unit_cost' => $saleItem->unit_price,
                    'total_cost' => $saleItem->quantity * $saleItem->unit_price,
                    'transaction_date' => now(),
                    'notes' => 'Devolución por eliminación de ítem de venta #' . $sale->invoice_number,
                    'user_id' => auth()->id(),
                    'sale_id' => $sale->id,
                ]);
            }

            // Delete the sale item
            $saleItem->delete();

            // Update sale totals
            $this->updateSaleTotals($sale);

            return response()->json([
                'message' => 'Ítem eliminado correctamente'
            ], 200);
        });
    }

    /**
     * Update sale totals based on its items
     */
    private function updateSaleTotals(Sale $sale)
    {
        $items = $sale->items()->get();
        
        $subtotal = $items->sum('subtotal');
        $tax = $items->sum('tax');
        $discount = $items->sum('discount');
        $total = $items->sum('total');

        $sale->update([
            'subtotal' => $subtotal,
            'tax' => $tax,
            'discount' => $discount,
            'total' => $total,
        ]);
    }
}
