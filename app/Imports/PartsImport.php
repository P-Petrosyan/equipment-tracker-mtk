<?php

namespace App\Imports;

use App\Models\Part;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class PartsImport implements ToModel, WithHeadingRow
{
    public function model(array $row)
    {
        return new Part([
            'code' => $row['code'],
            'name' => $row['name'],
            'unit_price' => $row['price'],
            'quantity' => $row['quantity'] ?? 0,
            'measure_unit' => $row['measure_unit'] ?? 'հատ',
        ]);
    }
}
