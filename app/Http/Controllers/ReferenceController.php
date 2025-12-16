<?php

namespace App\Http\Controllers;

use App\Models\Act;
use App\Models\Naming;
use App\Models\Partner;
use App\Models\Position;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use Maatwebsite\Excel\Facades\Excel;

class ReferenceController extends Controller
{
    public function index(Request $request)
    {
        $acts = collect();
        $startDate = $request->get('start_date');
        $endDate = $request->get('end_date');
        $naming = Naming::where('name', 'Պայմանագիր')->first();
        if ($startDate && $endDate) {
            $acts = Act::with('partner')
                ->whereBetween('act_date', [$startDate, $endDate])
                ->get();
        }

        return view('reference.index', compact('acts', 'naming', 'startDate', 'endDate'));
    }

    public function print(Request $request)
    {
        $startDate = $request->get('start_date');
        $endDate = $request->get('end_date');
        $acts = Act::with(['partner', 'works.equipment', 'works.equipmentPartGroup'])
            ->whereBetween('act_date', [$startDate, $endDate])
            ->join('partners', 'acts.partner_id', '=', 'partners.id')
            ->orderBy('partners.region')
            ->orderBy('acts.act_date', 'desc')
            ->select('acts.*')
            ->get();

        $tnoren = Position::where('title', 'Տնօրեն')->first();
        $naming = Naming::where('name', 'Պայմանագիր')->first();
        $vachNaxagah = Naming::where('id', 2)->first(); // Վարչության նախագահ-Գլխավոր տնօրեն

        $pdf = Pdf::loadView('reference.pdf', compact('acts', 'naming', 'vachNaxagah', 'tnoren', 'startDate', 'endDate'));
        $pdf->setPaper('A4', 'portrait');

        return $pdf->download('Գազպրոմ տեղեկանք ' . \Carbon\Carbon::parse($endDate)->format('d.m.Y') . '.pdf');
    }

    public function getPartnersByPeriod(Request $request)
    {
        $startDate = $request->get('start_date');
        $endDate = $request->get('end_date');

        if (!$startDate || !$endDate) {
            return response()->json(['partners' => []]);
        }

        $partners = Act::with('partner')
            ->whereBetween('act_date', [$startDate, $endDate])
            ->get()
            ->pluck('partner')
            ->unique('id')
            ->groupBy('region')
            ->map(function($regionPartners) {
                return $regionPartners->unique('id')->values();
            });

        return response()->json(['partners' => $partners]);
    }

    public function trilateral(Request $request)
    {
        $startDate = $request->get('start_date');
        $endDate = $request->get('end_date');

        $glxavorChartaraget = Naming::where('id', 3)->first();
        $tnoreniTexakal = Naming::where('id', 4)->first();
        $labVarich = Naming::where('id', 5)->first();
        $SCHAM = Naming::where('id', 6)->first();

        $tnoren = Position::where('title', 'Տնօրեն')->first();
        $labXekavar = Position::where('id', 1)->first();

        $acts = Act::with(['partner', 'works.equipment'])
            ->whereBetween('act_date', [$startDate, $endDate])
            ->get();

        // Group works by partner and calculate totals
        $partnerData = [];
        foreach ($acts as $act) {
            $partnerName = $act->partner->region;
            if (!isset($partnerData[$partnerName])) {
                $partnerData[$partnerName] = [
                    'name' => $partnerName,
                    'total_works' => 0,
                    'total_price' => 0
                ];
            }

            $repairableWorks = $act->works->where('non_repairable', 0);
            $partnerData[$partnerName]['total_works'] += $repairableWorks->count();

            foreach ($repairableWorks as $work) {
                $partnerData[$partnerName]['total_price'] += $work->equipment->stug_price ?? 0;
            }
        }

        $pdf = Pdf::loadView('reference.trilateral', compact('partnerData', 'glxavorChartaraget', 'tnoren', 'tnoreniTexakal', 'labXekavar', 'labVarich', 'SCHAM', 'startDate', 'endDate'));
        $pdf->setPaper('A4', 'portrait');

        return $pdf->download('ՍՉԱՄ Եռակողմ ակտ ' . \Carbon\Carbon::parse($endDate)->format('d.m.Y') . '.pdf');
    }

    public function exportPartsUsed(Request $request)
    {
        $partnerIds = explode(',', $request->get('partner_ids'));
        $startDate = $request->get('start_date');
        $endDate = $request->get('end_date');
        $partner = Partner::find($partnerIds[0]);

        return Excel::download(new \App\Exports\PartsUsedExport($partnerIds, $startDate, $endDate), $partner->region . ' ' . now()->format('d-m-y') . '.xlsx');
    }
}
