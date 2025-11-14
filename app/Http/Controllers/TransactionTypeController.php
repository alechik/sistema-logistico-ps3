<?php

namespace App\Http\Controllers;

use App\Models\TransactionType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class TransactionTypeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $transactionTypes = TransactionType::all();
        return response()->json(['data' => $transactionTypes], 200);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255|unique:transaction_types',
            'description' => 'nullable|string',
            'effect' => 'required|in:add,subtract',
            'active' => 'boolean',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $transactionType = TransactionType::create([
            'name' => $request->name,
            'description' => $request->description,
            'effect' => $request->effect,
            'active' => $request->has('active') ? $request->active : true,
        ]);

        return response()->json(['data' => $transactionType], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(TransactionType $transactionType)
    {
        return response()->json(['data' => $transactionType], 200);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, TransactionType $transactionType)
    {
        $validator = Validator::make($request->all(), [
            'name' => [
                'sometimes',
                'required',
                'string',
                'max:255',
                Rule::unique('transaction_types')->ignore($transactionType->id),
            ],
            'description' => 'nullable|string',
            'effect' => 'sometimes|required|in:add,subtract',
            'active' => 'sometimes|boolean',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        // Prevent updating default transaction types
        $defaultTypes = ['Entrada', 'Salida', 'Ajuste de inventario'];
        if (in_array($transactionType->name, $defaultTypes)) {
            return response()->json([
                'message' => 'No se pueden modificar los tipos de transacción predeterminados.'
            ], 422);
        }

        $transactionType->update($request->all());

        return response()->json(['data' => $transactionType], 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(TransactionType $transactionType)
    {
        // Prevent deleting default transaction types
        $defaultTypes = ['Entrada', 'Salida', 'Ajuste de inventario'];
        if (in_array($transactionType->name, $defaultTypes)) {
            return response()->json([
                'message' => 'No se pueden eliminar los tipos de transacción predeterminados.'
            ], 422);
        }

        // Check if any transaction is using this type
        if ($transactionType->transactions()->exists()) {
            return response()->json([
                'message' => 'No se puede eliminar el tipo de transacción porque hay transacciones asociadas a él.'
            ], 422);
        }

        $transactionType->delete();
        return response()->json(['message' => 'Tipo de transacción eliminado correctamente'], 200);
    }

    /**
     * Get active transaction types
     */
    public function getActive()
    {
        $transactionTypes = TransactionType::where('active', true)->get();
        return response()->json(['data' => $transactionTypes], 200);
    }
}
