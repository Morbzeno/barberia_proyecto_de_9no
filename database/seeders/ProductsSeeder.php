<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ProductsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Insert categories de barberia
        DB::table('categories')->insert([
            [
                'name' => 'Electronics',
                'description' => 'Electronic devices and gadgets',
                'tags' => json_encode(['gadgets', 'devices', 'tech']),
            ],
            [
                'name' => 'Clothing',
                'description' => 'Apparel and fashion items',
                'tags' => json_encode(['fashion', 'apparel', 'style']),
            ],
            [
                'name' => 'Books',
                'description' => 'Printed and digital books',
                'tags' => json_encode(['literature', 'reading', 'education']),
            ],
        ]);

        // Insert products de barberia
        DB::table('products')->insert([
            [
                'categoryID' => 1,
                'name' => 'Smartphone',
                'sell_price' => 699.99,
                'buy_price' => 500.00,
                'bar_code' => '1234567890123',
                'stock' => 50,
                'state' => 'ACTIVO',
                'description' => 'Latest model smartphone',
            ],
            [
                'categoryID' => 2,
                'name' => 'T-Shirt',
                'sell_price' => 19.99,
                'buy_price' => 10.00,
                'bar_code' => '9876543210987',
                'stock' => 100,
                'state' => 'ACTIVO',
                'description' => 'Comfortable cotton t-shirt',
            ],
            [
                'categoryID' => 3,
                'name' => 'Novel',
                'sell_price' => 14.99,
                'buy_price' => 7.00,
                'bar_code' => '4567890123456',
                'stock' => 75,
                'state' => 'ACTIVO',
                'description' => 'Bestselling fiction novel',
            ],
        ]);
    }
}