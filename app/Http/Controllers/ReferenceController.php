<?php

namespace App\Http\Controllers;

use App\Models\Act;
use App\Models\Naming;
use App\Models\Position;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;

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
            ->orderBy('act_date', 'desc')
            ->get();

        $tnoren = Position::where('title', 'Տնօրեն')->first();
        $naming = Naming::where('name', 'Պայմանագիր')->first();
        $vachNaxagah = Naming::where('id', 2)->first(); // Վարչության նախագահ-Գլխավոր տնօրեն

        $pdf = Pdf::loadView('reference.pdf', compact('acts', 'naming', 'vachNaxagah', 'tnoren', 'startDate', 'endDate'));
        $pdf->setPaper('A4', 'portrait');

        return $pdf->download('Գազպրոմ տեղեկանք ' . $endDate . '.pdf');
    }
}
