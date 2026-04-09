<?php

namespace App\Exports;

use App\Models\Act;
use Maatwebsite\Excel\Concerns\FromArray;

class TrilateralExcelExport implements FromArray
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
        $acts = Act::with(['partner', 'works.equipment'])
            ->whereBetween('act_date', [$this->startDate, $this->endDate])
            ->get();

        $data = [];
        $data[] = ['ՍՉԱՄ Սերիական համարնենր - ' . $this->startDate . ' - ' . $this->endDate];
        $data[] = ['Գործընկեր', 'Սարքի անվանում', 'Նոր սերիական համար'];
        $data[] = [];

        foreach ($acts as $act) {
            $repairableWorks = $act->works->where('non_repairable', 0);
            foreach ($repairableWorks as $work) {
                $data[] = [
                    $act->partner->region,
                    $work->equipment->name,
                    $work->new_serial_number ?? ''
                ];
            }
        }

        return $data;
    }
}
