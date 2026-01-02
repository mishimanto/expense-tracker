<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    public function __construct()
    {
        $this->middleware('admin');
    }

    public function users(Request $request)
    {
        $perPage = $request->get('per_page', 20);
        $users = User::paginate($perPage);
        
        return response()->json($users);
    }

    public function updateUser(Request $request, $id)
    {
        $user = User::findOrFail($id);
        
        $validated = $request->validate([
            'name' => 'sometimes|string|max:255',
            'email' => 'sometimes|email|unique:users,email,' . $id,
            'role' => 'sometimes|in:user,admin',
        ]);

        $user->update($validated);
        
        return response()->json($user);
    }

    public function deleteUser($id)
    {
        $user = User::findOrFail($id);
        
        // Prevent deleting yourself
        if ($user->id === auth()->id()) {
            return response()->json(['message' => 'Cannot delete your own account'], 422);
        }

        $user->delete();
        
        return response()->json(['message' => 'User deleted']);
    }

    public function adminDashboard(Request $request)
    {
        $stats = [
            'total_users' => User::count(),
            'total_transactions' => \App\Models\Transaction::count(),
            'total_income' => \App\Models\Transaction::where('type', 'income')->sum('amount'),
            'total_expense' => \App\Models\Transaction::where('type', 'expense')->sum('amount'),
        ];

        $recentUsers = User::orderBy('created_at', 'desc')->limit(10)->get();
        
        return response()->json([
            'stats' => $stats,
            'recent_users' => $recentUsers,
        ]);
    }
}