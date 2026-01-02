<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class CategoryController extends Controller
{
    public function index(Request $request)
    {
        $cacheKey = 'categories_all';
        
        $categories = Cache::remember($cacheKey, 3600, function () {
            return Category::orderBy('type')->orderBy('name')->get();
        });
        
        return response()->json($categories);
    }

    public function store(Request $request)
    {
        if (!$request->user()->isAdmin()) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:categories',
            'type' => 'required|in:income,expense',
            'color' => 'required|string|max:7',
            'icon' => 'required|string|max:50',
        ]);

        $category = Category::create($validated);
        
        Cache::forget('categories_all');

        return response()->json($category, 201);
    }

    public function update(Request $request, $id)
    {
        if (!$request->user()->isAdmin()) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $category = Category::findOrFail($id);
        
        $validated = $request->validate([
            'name' => 'sometimes|string|max:255|unique:categories,name,' . $id,
            'type' => 'sometimes|in:income,expense',
            'color' => 'sometimes|string|max:7',
            'icon' => 'sometimes|string|max:50',
        ]);

        $category->update($validated);
        
        Cache::forget('categories_all');

        return response()->json($category);
    }

    public function destroy(Request $request, $id)
    {
        if (!$request->user()->isAdmin()) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $category = Category::findOrFail($id);
        
        if ($category->transactions()->exists()) {
            return response()->json(['message' => 'Cannot delete category with transactions'], 422);
        }

        $category->delete();
        
        Cache::forget('categories_all');

        return response()->json(['message' => 'Category deleted']);
    }
}