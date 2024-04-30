<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Http\Requests\ProductRequest;
use App\Http\Resources\ProductResource;

class ProductController extends Controller
{
    public function index()
    {
        return response()->json([
            'products' => new ProductResource(Product::with('category')->get())
        ]);
    }

    public function show(Product $product)
    {
        return response()->json([
            'product' => new ProductResource($product)
        ]);
    }

    public function store(ProductRequest $request)
    {
        try {
            if ($request->hasFile('image')) {
                $image = $request->file('image');
                $path = $image->store('products');
            }

            $request->image = $path;

            $product = Product::create([
                'name' => $request->name,
                'description' => $request->description,
                'price' => $request->price,
                'category_id' => $request->category_id,
                'sku' => Str::slug($request->name),
                'image' => $path,
                'stock' => $request->stock
            ]);

            return response()->json([
                'product' => new ProductResource($product),
                'message' => 'Product created successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function update(ProductRequest $request, Product $product)
    {
        try {
            if ($request->hasFile('image')) {
                $image = $request->file('image');
                $path = $image->store('products');
            }

            $product->name = $request->name;
            $product->description = $request->description;
            $product->sku = Str::slug($request->name);
            $product->price = $request->price;
            $product->stock = $request->stock;
            $product->category_id = $request->category_id;

            if(isset($path)) $product->image = $path;

            $product->save();

            return response()->json([
                'product' => new ProductResource($product),
                'message' => 'Product updated successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function delete(Product $product)
    {
        try {
            if($product){
                $product->delete();
                return response()->json([
                    'message' => 'Product deleted successfully'
                ]);
            }
            return response()->json([
                'message' => 'Product not found'
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
