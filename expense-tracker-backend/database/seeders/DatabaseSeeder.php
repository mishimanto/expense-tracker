<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run()
    {
        // Create admin user
        User::create([
            'name' => 'Admin User',
            'email' => 'admin@gmail.com',
            'password' => Hash::make('shimanto'),
            'role' => 'admin',
        ]);

        // Create regular user
        User::create([
            'name' => 'Regular User',
            'email' => 'user@gmail.com',
            'password' => Hash::make('shimanto'),
            'role' => 'user',
        ]);

        // Create default categories
        $incomeCategories = [
            ['name' => 'Salary', 'type' => 'income', 'color' => '#10B981', 'icon' => '💰'],
            ['name' => 'Freelance', 'type' => 'income', 'color' => '#3B82F6', 'icon' => '💼'],
            ['name' => 'Investment', 'type' => 'income', 'color' => '#8B5CF6', 'icon' => '📈'],
            ['name' => 'Gift', 'type' => 'income', 'color' => '#EC4899', 'icon' => '🎁'],
        ];

        $expenseCategories = [
            ['name' => 'Food & Dining', 'type' => 'expense', 'color' => '#EF4444', 'icon' => '🍔'],
            ['name' => 'Transportation', 'type' => 'expense', 'color' => '#F59E0B', 'icon' => '🚗'],
            ['name' => 'Shopping', 'type' => 'expense', 'color' => '#8B5CF6', 'icon' => '🛍️'],
            ['name' => 'Entertainment', 'type' => 'expense', 'color' => '#EC4899', 'icon' => '🎬'],
            ['name' => 'Bills & Utilities', 'type' => 'expense', 'color' => '#3B82F6', 'icon' => '💡'],
            ['name' => 'Healthcare', 'type' => 'expense', 'color' => '#10B981', 'icon' => '🏥'],
            ['name' => 'Education', 'type' => 'expense', 'color' => '#6366F1', 'icon' => '📚'],
            ['name' => 'Other', 'type' => 'expense', 'color' => '#6B7280', 'icon' => '📦'],
        ];

        foreach (array_merge($incomeCategories, $expenseCategories) as $category) {
            Category::create($category);
        }
    }
}