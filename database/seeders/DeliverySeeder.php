<?php

namespace Database\Seeders;

use App\Models\Delivery;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DeliverySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $data = [
            [
                'name' => 'Post',
                'cost' => 10,
            ],
            [
                'name' => 'DHL',
                'cost' => 20
            ],
            [
                'name' => 'Custom',
                'cost' => 15,
            ],
        ];
        Delivery::truncate();

        foreach ($data as $datum) {
            Delivery::withoutGlobalScopes()->insert($datum);
        }
    }
}
