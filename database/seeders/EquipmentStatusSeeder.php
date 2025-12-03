<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\EquipmentStatus;

class EquipmentStatusSeeder extends Seeder
{
    public function run()
    {
        $statuses = [
            ['name' => 'Received', 'color' => 'yellow'],
            ['name' => 'In Progress', 'color' => 'blue'],
            ['name' => 'Finished', 'color' => 'green'],
        ];

        foreach ($statuses as $status) {
            EquipmentStatus::create($status);
        }
    }
}
