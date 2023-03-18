<?php

namespace Database\Seeders;

use App\Models\Payment;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class PaymentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $data = [
            [
                'name' => 'Cash',
                'cost' => 0,
            ],
            [
                'name' => 'Card',
                'cost' => 5,
            ],
            [
                'name' => 'Bank',
                'cost' => 10,
            ],
        ];
        Payment::truncate();

        foreach ($data as $datum) {
            Payment::withoutGlobalScopes()->insert($datum);
        }
    }
}
