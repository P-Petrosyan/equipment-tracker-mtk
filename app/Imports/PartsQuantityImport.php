<?php

namespace App\Imports;

use App\Models\Part;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class PartsQuantityImport implements ToCollection, WithHeadingRow
{
    public function collection(Collection $collection)
    {
        foreach ($collection as $row) {
            $part = Part::where('code', $row['code'])->first();
            
            if ($part && isset($row['quantity']) && is_numeric($row['quantity'])) {
                $part->increment('quantity', (int)$row['quantity']);
            }
        }
    }
}
