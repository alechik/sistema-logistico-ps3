<?php

namespace App\Http\Controllers;

use App\Models\ProductCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class ProductCategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $categories = ProductCategory::all();
        return response()->json(['data' => $categories], 200);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255|unique:product_categories',
            'description' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $category = ProductCategory::create([
            'name' => $request->name,
            'description' => $request->description,
        ]);

        return response()->json(['data' => $category], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(ProductCategory $productCategory)
    {
        return response()->json(['data' => $productCategory], 200);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, ProductCategory $productCategory)
    {
        $validator = Validator::make($request->all(), [
            'name' => [
                'sometimes',
                'required',
                'string',
                'max:255',
                Rule::unique('product_categories')->ignore($productCategory->id),
            ],
            'description' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        // Prevent updating default categories
        $defaultCategories = ['Electrónicos', 'Ropa', 'Alimentos', 'Hogar', 'Deportes'];
        if (in_array($productCategory->name, $defaultCategories)) {
            return response()->json([
                'message' => 'No se pueden modificar las categorías de producto predeterminadas.'
            ], 422);
        }

        $productCategory->update($request->all());

        return response()->json(['data' => $productCategory], 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(ProductCategory $productCategory)
    {
        // Prevent deleting default categories
        $defaultCategories = ['Electrónicos', 'Ropa', 'Alimentos', 'Hogar', 'Deportes'];
        if (in_array($productCategory->name, $defaultCategories)) {
            return response()->json([
                'message' => 'No se pueden eliminar las categorías de producto predeterminadas.'
            ], 422);
        }

        // Check if any product is using this category
        if ($productCategory->products()->exists()) {
            return response()->json([
                'message' => 'No se puede eliminar la categoría porque hay productos asignados a ella.'
            ], 422);
        }

        $productCategory->delete();
        return response()->json(['message' => 'Categoría de producto eliminada correctamente'], 200);
    }
}
