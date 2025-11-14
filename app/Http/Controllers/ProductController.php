<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\ProductCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        // Si es una petición API, devolver JSON
        if ($request->expectsJson() || $request->is('api/*')) {
            $products = Product::with('category')->get();
            return response()->json(['data' => $products], 200);
        }
        
        // Si es una petición web, devolver vista
        $categories = ProductCategory::all();
        return view('products.index', compact('categories'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'sku' => 'required|string|max:100|unique:products',
            'description' => 'nullable|string',
            'category_id' => 'required|exists:product_categories,id',
            'brand' => 'nullable|string|max:100',
            'model' => 'nullable|string|max:100',
            'barcode' => 'nullable|string|max:100|unique:products',
            'unit_of_measure' => 'required|string|max:50',
            'purchase_price' => 'required|numeric|min:0',
            'sale_price' => 'required|numeric|min:0',
            'stock' => 'required|integer|min:0',
            'minimum_stock' => 'required|integer|min:0',
            'maximum_stock' => 'required|integer|min:0|gt:minimum_stock',
            'location' => 'nullable|string|max:100',
            'notes' => 'nullable|string',
            'is_active' => 'boolean',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $product = Product::create([
            'name' => $request->name,
            'sku' => $request->sku,
            'description' => $request->description,
            'category_id' => $request->category_id,
            'brand' => $request->brand,
            'model' => $request->model,
            'barcode' => $request->barcode,
            'unit_of_measure' => $request->unit_of_measure,
            'purchase_price' => $request->purchase_price,
            'sale_price' => $request->sale_price,
            'stock' => $request->stock,
            'minimum_stock' => $request->minimum_stock,
            'maximum_stock' => $request->maximum_stock,
            'location' => $request->location,
            'notes' => $request->notes,
            'is_active' => $request->has('is_active') ? $request->is_active : true,
        ]);

        return response()->json(['data' => $product->load('category')], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Product $product)
    {
        return response()->json(['data' => $product->load('category')], 200);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Product $product)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'sometimes|required|string|max:255',
            'sku' => [
                'sometimes',
                'required',
                'string',
                'max:100',
                Rule::unique('products')->ignore($product->id),
            ],
            'description' => 'nullable|string',
            'category_id' => 'sometimes|required|exists:product_categories,id',
            'brand' => 'nullable|string|max:100',
            'model' => 'nullable|string|max:100',
            'barcode' => [
                'nullable',
                'string',
                'max:100',
                Rule::unique('products')->ignore($product->id),
            ],
            'unit_of_measure' => 'sometimes|required|string|max:50',
            'purchase_price' => 'sometimes|required|numeric|min:0',
            'sale_price' => 'sometimes|required|numeric|min:0',
            'stock' => 'sometimes|required|integer|min:0',
            'minimum_stock' => 'sometimes|required|integer|min:0',
            'maximum_stock' => 'sometimes|required|integer|min:0|gt:minimum_stock',
            'location' => 'nullable|string|max:100',
            'notes' => 'nullable|string',
            'is_active' => 'sometimes|boolean',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $product->update($request->all());

        return response()->json(['data' => $product->load('category')], 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Product $product)
    {
        // Check if product has inventory records
        if ($product->inventories()->exists()) {
            return response()->json([
                'message' => 'No se puede eliminar el producto porque tiene registros de inventario asociados.'
            ], 422);
        }

        // Check if product has sale items
        if ($product->saleItems()->exists()) {
            return response()->json([
                'message' => 'No se puede eliminar el producto porque está incluido en ventas.'
            ], 422);
        }

        $product->delete();
        return response()->json(['message' => 'Producto eliminado correctamente'], 200);
    }

    /**
     * Get products by category
     */
    public function getByCategory($categoryId)
    {
        $products = Product::where('category_id', $categoryId)
            ->where('is_active', true)
            ->get();
            
        return response()->json(['data' => $products], 200);
    }

    /**
     * Get low stock products
     */
    public function getLowStock()
    {
        $products = Product::whereRaw('stock <= minimum_stock')
            ->where('is_active', true)
            ->get();
            
        return response()->json(['data' => $products], 200);
    }
}
