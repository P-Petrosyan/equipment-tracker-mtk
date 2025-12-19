<?php

namespace App\Exports;

use App\Models\Act;
use Maatwebsite\Excel\Concerns\FromArray;

class PartsUsedExport implements FromArray
{
    protected $partnerIds;
    protected $startDate;
    protected $endDate;

    public function __construct($partnerIds, $startDate, $endDate)
    {
        $this->partnerIds = $partnerIds;
        $this->startDate = $startDate;
        $this->endDate = $endDate;
    }



    public function array(): array
    {
        $acts = Act::with(['partner', 'works.equipment', 'works.equipmentPartGroup.parts'])
            ->whereIn('partner_id', $this->partnerIds)
            ->whereBetween('act_date', [$this->startDate, $this->endDate])
            ->get();

        $data = [];
        foreach ($acts->groupBy('partner_id') as $partnerActs) {
            $partner = $partnerActs->first()->partner;
            $data[] = [$partner->region . ' '. $this->startDate . ' - ' . $this->endDate , '', '', '', ''];
            $data[] = ['Սարք', 'Գործարանային համար', 'Վերանորոգման արժեք' , 'Պահեստամասի (աշխատանքի) անվանում [ քանակ x միավորի գին = ընդհանուր գումար դր]'];

            foreach ($partnerActs as $act) {
                foreach ($act->works as $work) {
                    if ($work->equipmentPartGroup) {
                        $partsCalculation = [];
                        $totalPrice = 0;
                        foreach ($work->equipmentPartGroup->parts as $part) {
                            $partTotal = $part->pivot->quantity * ($part->pivot->unit_price ?? $part->unit_price );
                            $totalPrice += $partTotal;
                            $partsCalculation[] = $part->code . ' ' . $part->name . ' [' . number_format($part->pivot->quantity) . ' x ' . ($part->pivot->unit_price ?? $part->unit_price ). ' = ' . $partTotal . ']';
                        }

                        $data[] = [
                            $work->equipment->name,
                            $work->new_serial_number ?? '',
                            $work->equipmentPartGroup->total_price ?? '',
                            implode('; ', $partsCalculation),
                        ];
                    } else {
                        $data[] = [
                            $work->equipment->name,
                            $work->new_serial_number ?? '',
                            '-',
                            '-'
                        ];
                    }
                }
            }
            $data[] = ['', '', '', '', ''];
        }

        return $data;
    }
}
