<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class TransactionController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();
        $cacheKey = "user_{$user->id}_transactions";
        
        $transactions = Cache::remember($cacheKey, 300, function () use ($user, $request) {
            $query = $user->transactions()->with('category');
            
            if ($request->has('type')) {
                $query->where('type', $request->type);
            }
            
            if ($request->has('month')) {
                $query->whereMonth('date', $request->month);
            }
            
            if ($request->has('year')) {
                $query->whereYear('date', $request->year);
            }
            
            if ($request->has('category_id')) {
                $query->where('category_id', $request->category_id);
            }
            
            return $query->orderBy('date', 'desc')->paginate(20);
        });
        
        return response()->json($transactions);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'category_id' => 'required|exists:categories,id',
            'type' => 'required|in:income,expense',
            'amount' => 'required|numeric|min:0',
            'date' => 'required|date',
            'note' => 'nullable|string|max:500',
        ]);

        $transaction = $request->user()->transactions()->create($validated);
        
        // Clear cache
        Cache::forget("user_{$request->user()->id}_transactions");
        Cache::forget("user_{$request->user()->id}_dashboard");

        return response()->json($transaction->load('category'), 201);
    }

    public function show(Request $request, $id)
    {
        $transaction = $request->user()->transactions()->with('category')->findOrFail($id);
        return response()->json($transaction);
    }

    public function update(Request $request, $id)
    {
        $transaction = $request->user()->transactions()->findOrFail($id);
        
        $validated = $request->validate([
            'category_id' => 'sometimes|exists:categories,id',
            'type' => 'sometimes|in:income,expense',
            'amount' => 'sometimes|numeric|min:0',
            'date' => 'sometimes|date',
            'note' => 'nullable|string|max:500',
        ]);

        $transaction->update($validated);
        
        // Clear cache
        Cache::forget("user_{$request->user()->id}_transactions");
        Cache::forget("user_{$request->user()->id}_dashboard");

        return response()->json($transaction->load('category'));
    }

    public function destroy(Request $request, $id)
    {
        $transaction = $request->user()->transactions()->findOrFail($id);
        $transaction->delete();
        
        // Clear cache
        Cache::forget("user_{$request->user()->id}_transactions");
        Cache::forget("user_{$request->user()->id}_dashboard");

        return response()->json(['message' => 'Transaction deleted']);
    }
}