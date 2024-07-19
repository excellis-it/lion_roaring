<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DummyProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // dummy product data
        $products = [
            [
                'user_id' => 1,
                'name' => 'Product 1',
                'slug' => 'product-1',
                'description' => 'Product 1 description',
                'price' => 100,
                'status' => 1,
                'category_id' => 1,
            ],
            [
                'user_id' => 1,
                'name' => 'Product 2',
                'slug' => 'product-2',
                'description' => 'Product 2 description',
                'price' => 200,
                'status' => 1,
                'category_id' => 2,
            ],
            [
                'user_id' => 1,
                'name' => 'Product 3',
                'slug' => 'product-3',
                'description' => 'Product 3 description',
                'price' => 300,
                'status' => 1,
                'category_id' => 3,
            ],
            [
                'user_id' => 1,
                'name' => 'Product 4',
                'slug' => 'product-4',
                'description' => 'Product 4 description',
                'price' => 400,
                'status' => 1,
                'category_id' => 4,
            ],
            [
                'user_id' => 1,
                'name' => 'Product 5',
                'slug' => 'product-5',
                'description' => 'Product 5 description',
                'price' => 500,
                'status' => 1,
                'category_id' => 1,
            ],
            [
                'user_id' => 1,
                'name' => 'Product 6',
                'slug' => 'product-6',
                'description' => 'Product 6 description',
                'price' => 600,
                'status' => 1,
                'category_id' => 2,
            ],
            [
                'user_id' => 1,
                'name' => 'Product 7',
                'slug' => 'product-7',
                'description' => 'Product 7 description',
                'price' => 700,
                'status' => 1,
                'category_id' => 3,
            ],
            [
                'user_id' => 1,
                'name' => 'Product 8',
                'slug' => 'product-8',
                'description' => 'Product 8 description',
                'price' => 800,
                'status' => 1,
                'category_id' => 4,
            ],
            [
                'user_id' => 1,
                'name' => 'Product 9',
                'slug' => 'product-9',
                'description' => 'Product 9 description',
                'price' => 900,
                'status' => 1,
                'category_id' => 1,
            ],
            [
                'user_id' => 1,
                'name' => 'Product 10',
                'slug' => 'product-10',
                'description' => 'Product 10 description',
                'price' => 1000,
                'status' => 1,
                'category_id' => 2,
            ],
        ];

        // insert data into products table
        foreach ($products as $product) {
            \App\Models\Product::create($product);
        }
    }
}
