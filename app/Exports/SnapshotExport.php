<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class SnapshotExport implements FromCollection, WithHeadings
{
    protected $snapshotData;
    protected $snapshotDate;
    protected $snapshotComment;

    public function __construct($snapshotData, $snapshotDate, $snapshotComment = null)
    {
        $this->snapshotData = $snapshotData;
        $this->snapshotDate = $snapshotDate;
        $this->snapshotComment = $snapshotComment;
    }

    public function collection()
    {
        return collect($this->snapshotData)->map(function ($part) {
            return [
                'code' => $part['code'],
                'name' => $part['name'],
                'unit_price' => $part['unit_price'],
                'quantity' => $part['quantity'] ?? 0,
                'used_quantity' => $part['used_quantity'] ?? 0,
                'measure_unit' => $part['measure_unit'],
            ];
        });
    }

    public function headings(): array
    {
        return [
            'Code',
            'Name',
            'Unit Price',
            'Quantity',
            'Used Quantity',
            'Measure Unit',
        ];
    }
}