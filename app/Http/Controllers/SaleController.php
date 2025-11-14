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
use Illuminate\Validation\Rule;

class SaleController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        // Si es una petición API, devolver JSON
        if ($request->expectsJson() || $request->is('api/*')) {
            $sales = Sale::with(['items.product', 'warehouse', 'user'])
                ->orderBy('sale_date', 'desc')
                ->get();
            return response()->json(['data' => $sales], 200);
        }
        
        // Si es una petición web, devolver vista
        return view('sales.index');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), Sale::rules());

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        // Check inventory availability
        foreach ($request->items as $item) {
            $inventory = Inventory::where('product_id', $item['product_id'])
                ->where('warehouse_id', $request->warehouse_id)
                ->first();

            if (!$inventory || $inventory->quantity < $item['quantity']) {
                return response()->json([
                    'message' => 'No hay suficiente stock para el producto con ID: ' . $item['product_id']
                ], 422);
            }
        }

        // Start database transaction
        return DB::transaction(function () use ($request) {
            // Generate order number if not provided
            $orderNumber = $request->order_number ?? 'ORD-' . date('Ymd') . '-' . strtoupper(uniqid());
            
            // Calculate totals if not provided
            $subtotal = $request->subtotal ?? collect($request->items)->sum(function($item) {
                return ($item['unit_price'] * $item['quantity']) * (1 - ($item['discount'] / 100));
            });
            
            $tax = $request->tax ?? $subtotal * 0.18; // 18% IGV
            $total = $request->total ?? $subtotal + $tax - ($request->discount ?? 0);

            // Create sale
            $sale = Sale::create([
                'order_number' => $orderNumber,
                'sale_date' => $request->sale_date ?? now(),
                'subtotal' => $subtotal,
                'tax' => $tax,
                'discount' => $request->discount ?? 0,
                'total' => $total,
                'status' => $request->status ?? 'pendiente',
                'notes' => $request->notes,
                'user_id' => auth()->id(),
                'warehouse_id' => $request->warehouse_id,
                'customer_id' => $request->customer_id,
            ]);

            // Create sale items and update inventory
            foreach ($request->items as $item) {
                // Calculate item totals
                $itemSubtotal = $item['unit_price'] * $item['quantity'];
                $itemDiscount = ($itemSubtotal * $item['discount']) / 100;
                $itemTotal = $itemSubtotal - $itemDiscount;
                
                // Create sale item
                $saleItem = $sale->items()->create([
                    'product_id' => $item['product_id'],
                    'quantity' => $item['quantity'],
                    'unit_price' => $item['unit_price'],
                    'subtotal' => $itemSubtotal,
                    'tax' => $itemTotal * 0.18, // 18% IGV
                    'discount' => $itemDiscount,
                    'total' => $itemTotal,
                    'notes' => $item['notes'] ?? null,
                ]);

                // Update inventory
                $inventory = Inventory::where('product_id', $item['product_id'])
                    ->where('warehouse_id', $request->warehouse_id)
                    ->first();

                $inventory->decrement('quantity', $item['quantity']);
                $inventory->save();

                // Create inventory transaction
                $transactionType = TransactionType::where('name', 'Salida')->first();
                
                $inventory->transactions()->create([
                    'transaction_type_id' => $transactionType->id,
                    'quantity' => $item['quantity'],
                    'unit_cost' => $item['unit_price'],
                    'total_cost' => $item['total'],
                    'transaction_date' => now(),
                    'notes' => 'Venta #' . $sale->invoice_number,
                    'user_id' => auth()->id(),
                    'sale_id' => $sale->id,
                ]);
            }

            return response()->json([
                'message' => 'Venta registrada correctamente',
                'data' => $sale->load(['items.product', 'warehouse', 'user'])
            ], 201);
        });
    }

    /**
     * Display the specified resource.
     */
    public function show(Sale $sale)
    {
        return response()->json([
            'data' => $sale->load(['items.product', 'warehouse', 'user', 'customer'])
        ], 200);
    }

    /**
     * Update the specified resource in storage.
     */
    /**
     * Update the specified resource in storage.
     */
    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        try {
            $sale = Sale::with(['items', 'warehouse'])->findOrFail($id);

            // If sale is completed or cancelled, don't allow updates
            if (in_array($sale->status, ['completada', 'cancelada'])) {
                return response()->json([
                    'message' => 'No se puede actualizar una orden ' . $sale->status
                ], 422);
            }

            $validator = Validator::make($request->all(), Sale::rules($id));

            if ($validator->fails()) {
                return response()->json(['errors' => $validator->errors()], 422);
            }

            // Start database transaction
            return DB::transaction(function () use ($request, $sale) {
                // If status is being updated to 'cancelada', return items to inventory
                if ($request->status === 'cancelada') {
                    $this->returnItemsToInventory($sale);
                }
                
                // Update sale
                $sale->update($request->all());

                return response()->json([
                    'message' => 'Venta actualizada correctamente',
                    'data' => $sale->load(['items.product', 'warehouse', 'user'])
                ], 200);
            });
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error al actualizar la venta',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Return items to inventory when a sale is canceled
     * 
     * @param Sale $sale
     * @return void
     */
    protected function returnItemsToInventory(Sale $sale): void
    {
        $transactionType = TransactionType::where('name', 'Ajuste de inventario')->firstOrFail();
        
        foreach ($sale->items as $item) {
            $inventory = Inventory::where('product_id', $item->product_id)
                ->where('warehouse_id', $sale->warehouse_id)
                ->first();

            if ($inventory) {
                // Return items to inventory
                $inventory->increment('quantity', $item->quantity);

                // Create inventory transaction for cancellation
                $inventory->transactions()->create([
                    'transaction_type_id' => $transactionType->id,
                    'quantity' => $item->quantity,
                    'unit_cost' => $item->unit_price,
                    'total_cost' => $item->total,
                    'transaction_date' => now(),
                    'notes' => 'Devolución por cancelación de orden #' . $sale->order_number,
                    'user_id' => auth()->id(),
                    'sale_id' => $sale->id,
                ]);
            }
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Sale $sale)
    {
        try {
            // Only allow deletion of pending sales
            if ($sale->status !== 'pendiente') {
                return response()->json([
                    'message' => 'Solo se pueden eliminar ventas en estado pendiente.'
                ], 422);
            }

            return DB::transaction(function () use ($sale) {
                // Return items to inventory
                foreach ($sale->items as $item) {
                    $inventory = Inventory::where('product_id', $item->product_id)
                        ->where('warehouse_id', $sale->warehouse_id)
                        ->first();

                    if ($inventory) {
                        $inventory->increment('quantity', $item->quantity);
                    }
                }

                // Delete related records
                $sale->items()->delete();
                $sale->inventoryTransactions()->delete();
                $sale->delete();

                return response()->json([
                    'message' => 'Venta eliminada correctamente'
                ], 200);
            });
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error al eliminar la venta',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get sales by date range
     */
    /**
     * Get sales by date range
     */
    public function getByDateRange(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'warehouse_id' => 'nullable|exists:warehouses,id',
            'status' => 'nullable|in:pendiente,completada,cancelada',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $query = Sale::with(['items.product', 'warehouse', 'user'])
            ->whereBetween('sale_date', [$request->start_date, $request->end_date])
            ->orderBy('sale_date', 'desc');

        if ($request->has('warehouse_id')) {
            $query->where('warehouse_id', $request->warehouse_id);
        }

        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        $sales = $query->get();

        return response()->json(['data' => $sales], 200);
    }

    /**
     * Get sales summary
     */
    /**
     * Get sales summary
     */
    public function getSummary()
    {
        $summary = [
            'total_sales' => Sale::where('status', 'completada')->count(),
            'total_amount' => Sale::where('status', 'completed')->sum('total'),
            'today_sales' => Sale::whereDate('sale_date', today())->count(),
            'today_amount' => Sale::whereDate('sale_date', today())->sum('total'),
            'pending_sales' => Sale::where('status', 'pending')->count(),
            'cancelled_sales' => Sale::where('status', 'cancelled')->count(),
        ];

        return response()->json(['data' => $summary], 200);
    }
}
