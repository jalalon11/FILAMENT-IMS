<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Company;
use App\Models\Product;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Create admin user
        User::create([
            'name' => 'Admin',
            'email' => 'admin@ims.local',
            'password' => Hash::make('password'),
        ]);

        // Create categories
        $categories = [
            ['name' => 'Electronics', 'description' => 'Electronic devices and accessories'],
            ['name' => 'Office Supplies', 'description' => 'Stationery and office materials'],
            ['name' => 'Furniture', 'description' => 'Office and home furniture'],
            ['name' => 'Food & Beverages', 'description' => 'Consumable products'],
            ['name' => 'Cleaning Supplies', 'description' => 'Cleaning and maintenance items'],
        ];

        foreach ($categories as $category) {
            Category::create($category);
        }

        // Create companies
        $companies = [
            ['name' => 'Tech Solutions Inc.', 'address' => '123 Tech Street, Manila'],
            ['name' => 'Office World Corp.', 'address' => '456 Business Ave, Makati'],
            ['name' => 'Home Essentials Ltd.', 'address' => '789 Home Blvd, Quezon City'],
            ['name' => 'Fresh Goods Co.', 'address' => '321 Market Road, Pasig'],
        ];

        foreach ($companies as $company) {
            Company::create($company);
        }

        // Create sample products
        $products = [
            [
                'product_code' => 'ELEC-001',
                'product_name' => 'Wireless Mouse',
                'quantity' => 50,
                'category_id' => 1,
                'company_id' => 1,
                'cost_price' => 250.00,
                'selling_price' => 450.00,
                'date_received' => now()->subDays(10),
            ],
            [
                'product_code' => 'ELEC-002',
                'product_name' => 'USB Keyboard',
                'quantity' => 30,
                'category_id' => 1,
                'company_id' => 1,
                'cost_price' => 350.00,
                'selling_price' => 599.00,
                'date_received' => now()->subDays(8),
            ],
            [
                'product_code' => 'OFF-001',
                'product_name' => 'A4 Bond Paper (Ream)',
                'quantity' => 100,
                'category_id' => 2,
                'company_id' => 2,
                'cost_price' => 180.00,
                'selling_price' => 250.00,
                'date_received' => now()->subDays(5),
            ],
            [
                'product_code' => 'OFF-002',
                'product_name' => 'Ballpen (Box of 12)',
                'quantity' => 8,
                'category_id' => 2,
                'company_id' => 2,
                'cost_price' => 85.00,
                'selling_price' => 150.00,
                'date_received' => now()->subDays(15),
            ],
            [
                'product_code' => 'FURN-001',
                'product_name' => 'Office Chair',
                'quantity' => 15,
                'category_id' => 3,
                'company_id' => 3,
                'cost_price' => 2500.00,
                'selling_price' => 3999.00,
                'date_received' => now()->subDays(20),
            ],
            [
                'product_code' => 'FOOD-001',
                'product_name' => 'Bottled Water (Case)',
                'quantity' => 200,
                'category_id' => 4,
                'company_id' => 4,
                'cost_price' => 150.00,
                'selling_price' => 220.00,
                'date_received' => now()->subDays(2),
            ],
            [
                'product_code' => 'CLEAN-001',
                'product_name' => 'Hand Sanitizer (500ml)',
                'quantity' => 5,
                'category_id' => 5,
                'company_id' => 3,
                'cost_price' => 95.00,
                'selling_price' => 175.00,
                'date_received' => now()->subDays(7),
            ],
        ];

        foreach ($products as $productData) {
            Product::create($productData);
        }

        // Create sample transactions - need to bypass model events for initial seeding
        $transactions = [
            // Stock In transactions (these represent the initial stock)
            [
                'product_id' => 1,
                'type' => 'in',
                'quantity' => 50,
                'unit_price' => 250.00,
                'total_amount' => 12500.00,
                'reference_number' => 'PO-2024-001',
                'transaction_date' => now()->subDays(10),
            ],
            [
                'product_id' => 2,
                'type' => 'in',
                'quantity' => 30,
                'unit_price' => 350.00,
                'total_amount' => 10500.00,
                'reference_number' => 'PO-2024-002',
                'transaction_date' => now()->subDays(8),
            ],
            // Stock Out (Sales)
            [
                'product_id' => 1,
                'type' => 'out',
                'quantity' => 5,
                'unit_price' => 450.00,
                'total_amount' => 2250.00,
                'reference_number' => 'INV-2024-001',
                'transaction_date' => now()->subDays(3),
            ],
            [
                'product_id' => 3,
                'type' => 'out',
                'quantity' => 10,
                'unit_price' => 250.00,
                'total_amount' => 2500.00,
                'reference_number' => 'INV-2024-002',
                'transaction_date' => now()->subDays(2),
            ],
            [
                'product_id' => 6,
                'type' => 'out',
                'quantity' => 20,
                'unit_price' => 220.00,
                'total_amount' => 4400.00,
                'reference_number' => 'INV-2024-003',
                'transaction_date' => today(),
            ],
        ];

        // Insert transactions without triggering events (since products already have quantities set)
        foreach ($transactions as $transaction) {
            Transaction::withoutEvents(function () use ($transaction) {
                Transaction::create($transaction);
            });
        }
    }
}
