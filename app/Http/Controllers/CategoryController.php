<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;
use App\Http\Requests\CategoryRequest;
use App\Http\Resources\CategoryResource;

class CategoryController extends Controller
{
    public function index()
    {
        return response()->json([
            'categories' => new CategoryResource(Category::all())
        ]);
    }

    public function show(Category $category)
    {
        return response()->json([
            'category' => new CategoryResource($category)
        ]);
    }

    public function store(CategoryRequest $request)
    {
        try {
            $category = Category::create($request->all());
            return response()->json([
                'category' => new CategoryResource($category),
                'message' => 'Category created successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function update(CategoryRequest $request, Category $category)
    {
        try {
            $category->update($request->all());
            return response()->json([
                'category' => new CategoryResource($category),
                'message' => 'Category updated successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function delete(Category $category)
    {
        try {
            $category->delete();
            return response()->json([
                'message' => 'Category deleted successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
