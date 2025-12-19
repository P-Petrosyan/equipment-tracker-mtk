<?php

namespace App\Exports;

use App\Models\Act;
use Maatwebsite\Excel\Concerns\FromArray;

class ProductsByRegionsExport implements FromArray
{
    protected $startDate;
    protected $endDate;

    public function __construct($startDate, $endDate)
    {
        $this->startDate = $startDate;
        $this->endDate = $endDate;
    }

    public function array(): array
    {
        $acts = Act::with(['partner', 'works.equipmentPartGroup.parts'])
            ->whereBetween('act_date', [$this->startDate, $this->endDate])
            ->get();

        // Collect all products and partner data
        $partnerData = [];
        $allProducts = [];

        foreach ($acts as $act) {
            $partnerRegion = $act->partner->region;

            if (!isset($partnerData[$partnerRegion])) {
                $partnerData[$partnerRegion] = [];
            }

            foreach ($act->works as $work) {
                if ($work->equipmentPartGroup) {
                    foreach ($work->equipmentPartGroup->parts as $part) {
                        $productName = $part->code . ' ' . $part->name;
                        $allProducts[$productName] = true;

                        if (!isset($partnerData[$partnerRegion][$productName])) {
                            $partnerData[$partnerRegion][$productName] = [
                                'quantity' => 0,
                                'total_price' => 0
                            ];
                        }

                        $quantity = $part->pivot->quantity;
                        $unitPrice = $part->pivot->unit_price ?? $part->unit_price;

                        $partnerData[$partnerRegion][$productName]['quantity'] += $quantity;
                        $partnerData[$partnerRegion][$productName]['total_price'] += $quantity * $unitPrice;
                    }
                }
            }
        }

        $allProducts = array_keys($allProducts);

        // Build Excel data
        $data = [];
        
        // Title header
        $data[] = ['Ապրանքներ ըստ ԳԳՄ ' . $this->startDate . ' - ' . $this->endDate];
        
        // Empty row
        $data[] = [''];

        // Header row
        $header = [''];
        foreach ($allProducts as $product) {
            $header[] = $product;
            $header[] = '';
        }
        $header[] = '';
        $header[] = '';
        $data[] = $header;

        // Sub-header row
        $subHeader = [''];
        foreach ($allProducts as $product) {
            $subHeader[] = 'Օգտագործած քանակ';
            $subHeader[] = 'Ընդհանուր գին';
        }
        $subHeader[] = 'Քանակի հանրագումար';
        $subHeader[] = 'Գնի Հանրագումար';
        $data[] = $subHeader;

        // Partner rows
        foreach ($partnerData as $region => $products) {
            $row = [$region];
            $regionTotalQuantity = 0;
            $regionTotalPrice = 0;

            foreach ($allProducts as $product) {
                if (isset($products[$product])) {
                    $row[] = $products[$product]['quantity'];
                    $row[] = $products[$product]['total_price'];
                    $regionTotalQuantity += $products[$product]['quantity'];
                    $regionTotalPrice += $products[$product]['total_price'];
                } else {
                    $row[] = '';
                    $row[] = '';
                }
            }

            $row[] = $regionTotalQuantity;
            $row[] = $regionTotalPrice;
            $data[] = $row;
        }

        // Sum row
        $sumRow = ['SUM'];
        $grandTotalQuantity = 0;
        $grandTotalPrice = 0;

        foreach ($allProducts as $product) {
            $totalQuantity = 0;
            $totalPrice = 0;
            foreach ($partnerData as $products) {
                if (isset($products[$product])) {
                    $totalQuantity += $products[$product]['quantity'];
                    $totalPrice += $products[$product]['total_price'];
                }
            }
            $sumRow[] = $totalQuantity;
            $sumRow[] = $totalPrice;
            $grandTotalQuantity += $totalQuantity;
            $grandTotalPrice += $totalPrice;
        }

        $sumRow[] = $grandTotalQuantity;
        $sumRow[] = $grandTotalPrice;
        $data[] = $sumRow;

        return $data;
    }
}
