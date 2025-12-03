<?php

namespace App\Http\Controllers;

use App\Models\Partner;
use App\Models\Equipment;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\PartnerReportExport;

class ReportController extends Controller
{
    public function index()
    {
        $partners = Partner::orderBy('name')->get();
        return view('reports.index', compact('partners'));
    }

    public function generate(Request $request)
    {
        $request->validate([
            'partner_id' => 'required|exists:partners,id',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'date_type' => 'required|in:received_at,work_completed',
            'format' => 'required|in:pdf,excel',
        ]);

        $partner = Partner::findOrFail($request->partner_id);
        $startDate = $request->start_date;
        $endDate = $request->end_date;
        $dateType = $request->date_type;

        $query = Equipment::where('partner_id', $partner->id)
            ->with(['workOrders.parts', 'parts', 'status']);

        if ($dateType === 'received_at') {
            $query->whereBetween('received_at', [$startDate, $endDate]);
        } else {
            // Work completed date - check if any work order ended in range
            $query->whereHas('workOrders', function ($q) use ($startDate, $endDate) {
                $q->whereBetween('end_date', [$startDate, $endDate]);
            });
        }

        $equipment = $query->get();

        // Calculate totals
        $totalLabor = 0;
        $totalParts = 0;
        $grandTotal = 0;

        foreach ($equipment as $item) {
            $itemLabor = $item->workOrders->sum('labor_cost');
            $itemParts = $item->parts->sum('total_price') + $item->workOrders->flatMap->parts->sum('total_price');

            $item->total_labor = $itemLabor;
            $item->total_parts = $itemParts;
            $item->grand_total = $itemLabor + $itemParts;

            $totalLabor += $itemLabor;
            $totalParts += $itemParts;
        }

        $grandTotal = $totalLabor + $totalParts;

        $data = [
            'partner' => $partner,
            'equipment' => $equipment,
            'startDate' => $startDate,
            'endDate' => $endDate,
            'totalLabor' => $totalLabor,
            'totalParts' => $totalParts,
            'grandTotal' => $grandTotal,
        ];

        if ($request->format === 'pdf') {
            $pdf = Pdf::loadView('reports.pdf', $data);
            return $pdf->download('report_' . $partner->id . '_' . date('Ymd') . '.pdf');
        } else {
            return Excel::download(new PartnerReportExport($data), 'report_' . $partner->id . '_' . date('Ymd') . '.xlsx');
        }
    }
}
