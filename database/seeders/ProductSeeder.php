<?php

namespace Database\Seeders;

use App\Models\Product;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $data = [
            [
                'title'    => 'Product 1',
                'slug'    => 'product-1',
                'cost'     => 1100.50,
                'quantity' => 10,
            ],
            [
                'title'    => 'Product 2',
                'slug'    => 'product-2',
                'cost'     => 2100,
                'quantity' => 15,
            ],
            [
                'title'    => 'Product 3',
                'slug'    => 'product-3',
                'cost'     => 2050.70,
                'quantity' => 5,
            ],
        ];
        Product::truncate();

        foreach ($data as $datum) {
            Product::withoutGlobalScopes()->insert($datum);
        }
    }
}
