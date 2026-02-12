<?php

namespace App\Http\Controllers;

use App\Models\Act;
use App\Models\Naming;
use App\Models\Partner;
use App\Models\Position;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use Maatwebsite\Excel\Facades\Excel;
use PhpOffice\PhpWord\PhpWord;
use PhpOffice\PhpWord\IOFactory;

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

        $armenianMonths = [
            'January' => 'հունվար', 'February' => 'փետրվար', 'March' => 'մարտ',
            'April' => 'ապրիլ', 'May' => 'մայիս', 'June' => 'հունիս',
            'July' => 'հուլիս', 'August' => 'օգոստոս', 'September' => 'սեպտեմբեր',
            'October' => 'հոկտեմբեր', 'November' => 'նոյեմբեր', 'December' => 'դեկտեմբեր'
        ];

        $tnoren = Position::where('title', 'Տնօրեն')->first();
        $naming = Naming::where('name', 'Պայմանագիր')->first();
        $vachNaxagah = Naming::where('id', 2)->first(); // Վարչության նախագահ-Գլխավոր տնօրեն

        $pdf = Pdf::loadView('reference.pdf', compact('acts', 'naming', 'armenianMonths', 'vachNaxagah', 'tnoren', 'startDate', 'endDate'));
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

        $armenianMonths = [
            'January' => 'հունվար', 'February' => 'փետրվար', 'March' => 'մարտ',
            'April' => 'ապրիլ', 'May' => 'մայիս', 'June' => 'հունիս',
            'July' => 'հուլիս', 'August' => 'օգոստոս', 'September' => 'սեպտեմբեր',
            'October' => 'հոկտեմբեր', 'November' => 'նոյեմբեր', 'December' => 'դեկտեմբեր'
        ];

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

        $pdf = Pdf::loadView('reference.trilateral', compact('partnerData', 'glxavorChartaraget', 'tnoren', 'tnoreniTexakal', 'labXekavar', 'labVarich', 'SCHAM', 'armenianMonths', 'startDate', 'endDate'));
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

    public function exportProductsByRegions(Request $request)
    {
        $startDate = $request->get('start_date');
        $endDate = $request->get('end_date');

        return Excel::download(new \App\Exports\ProductsByRegionsExport($startDate, $endDate), 'Դետալների ծախս ընդհանուր ' . now()->format('d-m-y') . '.xlsx');
    }
    public function trilateralWord(Request $request)
    {
        $startDate = $request->get('start_date');
        $endDate = $request->get('end_date');

        $glxavorChartaraget = Naming::where('id', 3)->first();
        $tnoreniTexakal = Naming::where('id', 4)->first();
        $labVarich = Naming::where('id', 5)->first();
        $SCHAM = Naming::where('id', 6)->first();

        $tnoren = Position::where('title', 'Տնօրեն')->first();
        $labXekavar = Position::where('id', 1)->first();

        $armenianMonths = [
            'January' => 'հունվար', 'February' => 'փետրվար', 'March' => 'մարտ',
            'April' => 'ապրիլ', 'May' => 'մայիս', 'June' => 'հունիս',
            'July' => 'հուլիս', 'August' => 'օգոստոս', 'September' => 'սեպտեմբեր',
            'October' => 'հոկտեմբեր', 'November' => 'նոյեմբեր', 'December' => 'դեկտեմբեր'
        ];

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

        $phpWord = new PhpWord();
        $section = $phpWord->addSection(['marginTop' => 1134, 'marginBottom' => 1134, 'marginLeft' => 1134, 'marginRight' => 1134]);
        
        // Header - exactly as PDF
        $section->addText('ՀԱՍՏԱՏՈՒՄ ԵՄ', ['bold' => true, 'size' => 14], ['alignment' => 'center']);
        $section->addText('«Գազպրոմ Արմենիա» ՓԲԸ', ['bold' => true, 'size' => 14], ['alignment' => 'center']);
        $section->addText($glxavorChartaraget->name, ['size' => 12], ['alignment' => 'center']);
        $section->addText('______________________________ ' . $glxavorChartaraget->text, ['size' => 12], ['alignment' => 'center']);
        $section->addText('«____» _____________ ' . \Carbon\Carbon::parse($endDate)->format('Y') . 'թ.', ['size' => 12], ['alignment' => 'center']);
        $section->addTextBreak(2);
        
        // Approval sections table
        $approvalTable = $section->addTable(['width' => 100, 'unit' => 'pct']);
        $approvalTable->addRow();
        
        $leftCell = $approvalTable->addCell(4000, ['alignment' => 'center']);
        $leftCell->addText('ՀԱՍՏԱՏՈՒՄ ԵՄ', ['bold' => true], ['alignment' => 'center']);
        $leftCell->addText('«ՄՏԿ» ՓԲԸ ' . $tnoren->title, [], ['alignment' => 'center']);
        $leftCell->addText('_____________________ ' . $tnoren->titleholder, [], ['alignment' => 'center']);
        $leftCell->addText('«____» _____________ ' . \Carbon\Carbon::parse($endDate)->format('Y') . 'թ.', [], ['alignment' => 'center']);
        
        $approvalTable->addCell(2000); // spacer
        
        $rightCell = $approvalTable->addCell(4000, ['alignment' => 'center']);
        $rightCell->addText('ՀԱՍՏԱՏՈՒՄ ԵՄ', ['bold' => true], ['alignment' => 'center']);
        $rightCell->addText('«Ստանդարտացման և չափագիտության', [], ['alignment' => 'center']);
        $rightCell->addText('ազգային մարմին» ՓԲԸ ' . $tnoreniTexakal->name, [], ['alignment' => 'center']);
        $rightCell->addText('_____________________ ' . $tnoreniTexakal->text, [], ['alignment' => 'center']);
        $rightCell->addText('«____» _____________ ' . \Carbon\Carbon::parse($endDate)->format('Y') . 'թ.', [], ['alignment' => 'center']);
        
        $section->addTextBreak(2);
        
        // Act title
        $section->addText('ԱԿՏ', ['bold' => true, 'size' => 16], ['alignment' => 'center']);
        $section->addText('ԱՇԽԱՏԱՆՔՆԵՐի ՀԱՆՁՆՄԱՆ-ԸՆԴՈՒՆՄԱՆ', ['bold' => true, 'size' => 16], ['alignment' => 'center']);
        $section->addTextBreak();
        
        // Date and location
        $dateTable = $section->addTable(['width' => 100, 'unit' => 'pct']);
        $dateTable->addRow();
        $dateTable->addCell(5000)->addText(\Carbon\Carbon::parse($endDate)->format('d.m.Y') . 'թ.');
        $dateTable->addCell(5000)->addText('«ՄՏԿ» ՓԲԸ', [], ['alignment' => 'right']);
        
        $section->addTextBreak();
        
        // Main text
        $mainText = 'Մենք` ներքոստորագրողներս, «ՄՏԿ» ՓԲԸ գազի ձայնաազդանշանային սարքերի ստուգաչափման աջակցության ' . $labXekavar->title . ' ' . $labXekavar->titleholder . 'ը, «Ստանդարտացման և չափագիտության ազգային մարմին» ՓԲԸ ' . $labVarich->name . ' ' . $labVarich->text . 'ը կազմեցինք սույն ակտն այն մասին, որ ' . \Carbon\Carbon::parse($endDate)->format('Y') . 'թ. ' . $armenianMonths[\Carbon\Carbon::parse($endDate)->format('F')] . ' ամսվա ընթացքում, համաձայն «Գազպրոմ Արմենիա» ՓԲԸ և «Ստանդարտացման և չափագիտության ազգային մարմին» ՓԲԸ միջև ' . $SCHAM->text . ' պայմանագրի «ՄՏԿ» ՓԲԸ-ում կատարվել է HENAN CHICHENG ELECTRIC CO. LTD ընկերության արտադրության HD2000 տիպի ձայնաազդանշանային սարքերի ստուգաչափում հետևյալ քանակներով, որի համար ստորագրում ենք:';
        $section->addText($mainText, ['size' => 12], ['alignment' => 'both']);
        $section->addTextBreak();
        
        // Data table - exactly as PDF
        $table = $section->addTable(['borderSize' => 6, 'borderColor' => '000000', 'cellMargin' => 80]);
        
        // Header row 1
        $table->addRow(400);
        $table->addCell(800, ['vMerge' => 'restart', 'borderSize' => 6])->addText('Nº', ['bold' => true, 'size' => 10], ['alignment' => 'center']);
        $table->addCell(4000, ['vMerge' => 'restart', 'borderSize' => 6])->addText('ԳԳՄ-ի անվանումը', ['bold' => true, 'size' => 10], ['alignment' => 'center']);
        $table->addCell(2500, ['gridSpan' => 2, 'borderSize' => 6])->addText('HD2000', ['bold' => true, 'size' => 10], ['alignment' => 'center']);
        
        // Header row 2
        $table->addRow(400);
        $table->addCell(800, ['vMerge' => 'continue', 'borderSize' => 6]);
        $table->addCell(4000, ['vMerge' => 'continue', 'borderSize' => 6]);
        $table->addCell(1250, ['borderSize' => 6])->addText('Քանակը (հատ)', ['bold' => true, 'size' => 10], ['alignment' => 'center']);
        $table->addCell(1250, ['borderSize' => 6])->addText('Գինը (դրամ) առանց ԱԱՀ', ['bold' => true, 'size' => 10], ['alignment' => 'center']);
        
        // Data rows
        $totalWorks = 0;
        $totalPrice = 0;
        $index = 1;
        foreach ($partnerData as $partner) {
            $table->addRow(300);
            $table->addCell(800, ['borderSize' => 6])->addText($index++, ['size' => 10], ['alignment' => 'center']);
            $table->addCell(4000, ['borderSize' => 6])->addText($partner['name'], ['size' => 10]);
            $table->addCell(1250, ['borderSize' => 6])->addText($partner['total_works'], ['size' => 10], ['alignment' => 'center']);
            $table->addCell(1250, ['borderSize' => 6])->addText(number_format($partner['total_price'], 0), ['size' => 10], ['alignment' => 'center']);
            
            $totalWorks += $partner['total_works'];
            $totalPrice += $partner['total_price'];
        }
        
        // Total row
        $table->addRow(300);
        $table->addCell(800, ['borderSize' => 6]);
        $table->addCell(4000, ['borderSize' => 6])->addText('Ընդամենը', ['bold' => true, 'size' => 10]);
        $table->addCell(1250, ['borderSize' => 6])->addText($totalWorks, ['bold' => true, 'size' => 10], ['alignment' => 'center']);
        $table->addCell(1250, ['borderSize' => 6])->addText(number_format($totalPrice, 0), ['bold' => true, 'size' => 10], ['alignment' => 'center']);
        
        $section->addTextBreak(3);
        
        // Signature table - exactly as PDF
        $signatureTable = $section->addTable(['width' => 100, 'unit' => 'pct']);
        $signatureTable->addRow();
        
        $leftSig = $signatureTable->addCell(4000, ['alignment' => 'center']);
        $leftSig->addText('«ՄՏԿ» ՓԲԸ', ['bold' => true], ['alignment' => 'center']);
        $leftSig->addText('Գազի ձայնաազդանշանային սարքերի', [], ['alignment' => 'center']);
        $leftSig->addText('ստուգաչափման աջակցության', [], ['alignment' => 'center']);
        $leftSig->addText($labXekavar->title, [], ['alignment' => 'center']);
        $leftSig->addText($labXekavar->titleholder, [], ['alignment' => 'center']);
        $leftSig->addTextBreak();
        $leftSig->addText('______________________', [], ['alignment' => 'center']);
        $leftSig->addText('ստորագրություն', [], ['alignment' => 'center']);
        
        $signatureTable->addCell(2000); // spacer
        
        $rightSig = $signatureTable->addCell(4000, ['alignment' => 'center']);
        $rightSig->addText('«Ստանդարտացման և չափագիտության', ['bold' => true], ['alignment' => 'center']);
        $rightSig->addText('ազգային մարմին» ՓԲԸ', ['bold' => true], ['alignment' => 'center']);
        $rightSig->addText($labVarich->name, [], ['alignment' => 'center']);
        $rightSig->addText($labVarich->text, [], ['alignment' => 'center']);
        $rightSig->addTextBreak(2);
        $rightSig->addText('______________________', [], ['alignment' => 'center']);
        $rightSig->addText('ստորագրություն', [], ['alignment' => 'center']);

        $filename = 'ՍՉԱՄ Եռակողմ ակտ ' . \Carbon\Carbon::parse($endDate)->format('d.m.Y') . '.docx';
        $tempFile = tempnam(sys_get_temp_dir(), 'word');

        $writer = IOFactory::createWriter($phpWord, 'Word2007');
        $writer->save($tempFile);

        return response()->download($tempFile, $filename)->deleteFileAfterSend();
    }
}
