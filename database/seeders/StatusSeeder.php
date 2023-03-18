<?php

namespace Database\Seeders;

use App\Models\Status;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class StatusSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $statuses = ['inactive', 'active', 'pending', 'paid', 'collected', 'in progress', 'delivered'];
        Status::truncate();

        foreach ($statuses as $status) {
            Status::withoutGlobalScopes()->insert(['name' => $status]);
        }
    }
}
