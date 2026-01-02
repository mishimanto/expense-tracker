<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class ReportController extends Controller
{
    public function dashboard(Request $request)
    {
        $user = $request->user();
        $cacheKey = "user_{$user->id}_dashboard";
        
        $data = Cache::remember($cacheKey, 300, function () use ($user) {
            $currentMonth = now()->format('Y-m');
            $lastMonth = now()->subMonth()->format('Y-m');
            
            // Current month totals
            $currentMonthData = Transaction::where('user_id', $user->id)
                ->whereRaw("DATE_FORMAT(date, '%Y-%m') = ?", [$currentMonth])
                ->selectRaw('SUM(CASE WHEN type = "income" THEN amount ELSE 0 END) as total_income')
                ->selectRaw('SUM(CASE WHEN type = "expense" THEN amount ELSE 0 END) as total_expense')
                ->first();
            
            // Last month totals
            $lastMonthData = Transaction::where('user_id', $user->id)
                ->whereRaw("DATE_FORMAT(date, '%Y-%m') = ?", [$lastMonth])
                ->selectRaw('SUM(CASE WHEN type = "income" THEN amount ELSE 0 END) as last_month_income')
                ->selectRaw('SUM(CASE WHEN type = "expense" THEN amount ELSE 0 END) as last_month_expense')
                ->first();
            
            // Monthly trend (last 6 months)
            $monthlyTrend = Transaction::where('user_id', $user->id)
                ->where('date', '>=', now()->subMonths(6)->startOfMonth())
                ->selectRaw("DATE_FORMAT(date, '%Y-%m') as month")
                ->selectRaw('SUM(CASE WHEN type = "income" THEN amount ELSE 0 END) as income')
                ->selectRaw('SUM(CASE WHEN type = "expense" THEN amount ELSE 0 END) as expense')
                ->groupBy('month')
                ->orderBy('month')
                ->get();
            
            // Category-wise expenses (current month)
            $categoryExpenses = Transaction::where('user_id', $user->id)
                ->where('type', 'expense')
                ->whereRaw("DATE_FORMAT(date, '%Y-%m') = ?", [$currentMonth])
                ->join('categories', 'transactions.category_id', '=', 'categories.id')
                ->select('categories.name', 'categories.color', DB::raw('SUM(amount) as total'))
                ->groupBy('categories.id', 'categories.name', 'categories.color')
                ->orderByDesc('total')
                ->limit(8)
                ->get();
            
            // Recent transactions
            $recentTransactions = $user->transactions()
                ->with('category')
                ->orderBy('date', 'desc')
                ->limit(10)
                ->get();
            
            return [
                'current_month' => [
                    'income' => (float) ($currentMonthData->total_income ?? 0),
                    'expense' => (float) ($currentMonthData->total_expense ?? 0),
                    'balance' => (float) (($currentMonthData->total_income ?? 0) - ($currentMonthData->total_expense ?? 0)),
                ],
                'last_month' => [
                    'income' => (float) ($lastMonthData->last_month_income ?? 0),
                    'expense' => (float) ($lastMonthData->last_month_expense ?? 0),
                    'balance' => (float) (($lastMonthData->last_month_income ?? 0) - ($lastMonthData->last_month_expense ?? 0)),
                ],
                'monthly_trend' => $monthlyTrend,
                'category_expenses' => $categoryExpenses,
                'recent_transactions' => $recentTransactions,
            ];
        });
        
        return response()->json($data);
    }

    public function monthlyReport(Request $request)
    {
        $request->validate([
            'year' => 'required|integer|min:2000|max:2100',
            'month' => 'required|integer|min:1|max:12',
        ]);

        $user = $request->user();
        $cacheKey = "user_{$user->id}_report_{$request->year}_{$request->month}";
        
        $report = Cache::remember($cacheKey, 300, function () use ($user, $request) {
            $startDate = "{$request->year}-{$request->month}-01";
            $endDate = date('Y-m-t', strtotime($startDate));
            
            // Daily breakdown
            $dailyData = Transaction::where('user_id', $user->id)
                ->whereBetween('date', [$startDate, $endDate])
                ->selectRaw('DATE(date) as day')
                ->selectRaw('SUM(CASE WHEN type = "income" THEN amount ELSE 0 END) as income')
                ->selectRaw('SUM(CASE WHEN type = "expense" THEN amount ELSE 0 END) as expense')
                ->groupBy('day')
                ->orderBy('day')
                ->get();
            
            // Category breakdown
            $categoryData = Transaction::where('user_id', $user->id)
                ->whereBetween('date', [$startDate, $endDate])
                ->join('categories', 'transactions.category_id', '=', 'categories.id')
                ->select('categories.name', 'categories.type', 'categories.color', DB::raw('SUM(amount) as total'))
                ->groupBy('categories.id', 'categories.name', 'categories.type', 'categories.color')
                ->orderByDesc('total')
                ->get();
            
            // Top expenses
            $topExpenses = $user->transactions()
                ->with('category')
                ->where('type', 'expense')
                ->whereBetween('date', [$startDate, $endDate])
                ->orderBy('amount', 'desc')
                ->limit(10)
                ->get();
            
            return [
                'daily_data' => $dailyData,
                'category_data' => $categoryData,
                'top_expenses' => $topExpenses,
            ];
        });
        
        return response()->json($report);
    }
}