<?php

use App\Http\Controllers\DefectController;
use App\Http\Controllers\EquipmentPartGroupController;
use App\Http\Controllers\NamingController;
use App\Http\Controllers\PartnerController;
use App\Http\Controllers\EquipmentController;
use App\Http\Controllers\PartnerStructureController;
use App\Http\Controllers\PositionController;
use App\Http\Controllers\ReasonController;
use App\Http\Controllers\WorkOrderController;
use App\Http\Controllers\PartController;
use App\Http\Controllers\WorkController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\ActController;
use App\Http\Controllers\ReferenceController;
use App\Models\Defect;
use App\Models\Equipment;
use App\Models\Naming;
use App\Models\Part;
use App\Models\Partner;
use App\Models\Position;
use App\Models\Reason;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('dashboard');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->name('dashboard');

Route::get('/general-data/{tab?}', function ($tab = 'partners') {
    $data = [];

    switch($tab) {
        case 'partners':
            $data['partners'] = Partner::with('structures')->get();
            break;
        case 'counters':
            $data['equipment'] = Equipment::with('partGroups.parts')->get();
            $data['all_parts'] = Part::all();
            break;
        case 'reasons':
            $data['reasons'] = Reason::all();
        case 'defects':
            $data['defects'] = Defect::all();
        case 'positions':
            $data['positions'] = Position::all();
        case 'calendar':
        case 'namings':
            $data['namings'] = Naming::all();
        case 'parts':
            $data['parts'] = Part::all();
            break;
        case 'snapshots':
            $data['snapshots'] = \App\Models\PartsSnapshot::orderBy('snapshot_date', 'desc')->get();
            break;
        default:
            $tab = 'partners';
            $data['partners'] = \App\Models\Partner::with('structures')->get();
    }

    return view('general_data', compact('data', 'tab'));
})->name('general-data');

Route::resource('partners', PartnerController::class);
//Route::patch('partners/quickUpdate/{partner}', [PartnerController::class, 'updateFromInput'])->name('partners.updateFromInput');

Route::patch('equipment/{equipment}/status', [EquipmentController::class, 'updateStatus'])->name('equipment.update-status');
Route::resource('equipment', EquipmentController::class);
//Route::resource('work-orders', WorkOrderController::class);
//Route::resource('parts', PartController::class);
//Route::resource('equipment-statuses', \App\Http\Controllers\EquipmentStatusController::class);

Route::post('/partner-structures', [PartnerStructureController::class, 'store'])->name('partner-structures.store');
Route::delete('/partner-structures/{partnerStructure}', [PartnerStructureController::class, 'destroy'])->name('partner-structures.destroy');

Route::post('/reasons', [ReasonController::class, 'store'])->name('reasons.store');
Route::patch('/reasons/{reason}', [ReasonController::class, 'update'])->name('reasons.update');
Route::delete('/reasons/{reason}', [ReasonController::class, 'destroy'])->name('reasons.destroy');

Route::post('/defects', [DefectController::class, 'store'])->name('defects.store');
Route::patch('/defects/{defect}', [DefectController::class, 'update'])->name('defects.update');
Route::delete('/defects/{defect}', [DefectController::class, 'destroy'])->name('defects.destroy');

Route::post('/positions', [PositionController::class, 'store'])->name('positions.store');
Route::patch('/positions/{position}', [PositionController::class, 'update'])->name('positions.update');
Route::delete('/positions/{position}', [PositionController::class, 'destroy'])->name('positions.destroy');

Route::post('/namings', [NamingController::class, 'store'])->name('namings.store');
Route::patch('/namings/{naming}', [NamingController::class, 'update'])->name('namings.update');
Route::delete('/namings/{naming}', [NamingController::class, 'destroy'])->name('namings.destroy');

Route::get('/parts', [PartController::class, 'index'])->name('parts.index');
Route::post('/parts', [PartController::class, 'store'])->name('parts.store');
Route::patch('/parts/{part}', [PartController::class, 'update'])->name('parts.update');
Route::delete('/parts/{part}', [PartController::class, 'destroy'])->name('parts.destroy');
Route::post('/parts/{part}/add-quantity', [PartController::class, 'addQuantity'])->name('parts.add-quantity');
Route::get('/parts/export', [PartController::class, 'export'])->name('parts.export');
Route::post('/parts/import', [PartController::class, 'import'])->name('parts.import');
Route::post('/parts/import-quantities', [PartController::class, 'importQuantities'])->name('parts.import-quantities');
Route::post('/parts/create-snapshot', [PartController::class, 'createSnapshot'])->name('parts.create-snapshot');
Route::get('/parts/snapshots', [PartController::class, 'getSnapshots'])->name('parts.snapshots');
Route::get('/parts/snapshot/{date}', [PartController::class, 'viewSnapshot'])->name('parts.view-snapshot');
Route::get('/parts/snapshot-data/{date}', [PartController::class, 'getSnapshotData'])->name('parts.snapshot-data');
Route::get('/parts/snapshot-export/{date}', [PartController::class, 'exportSnapshot'])->name('parts.snapshot-export');

Route::post('/equipment-part-groups', [EquipmentPartGroupController::class, 'store'])->name('equipment-part-groups.store');
Route::patch('/equipment-part-groups/{equipmentPartGroup}', [EquipmentPartGroupController::class, 'update'])->name('equipment-part-groups.update');
Route::delete('/equipment-part-groups/{equipmentPartGroup}', [EquipmentPartGroupController::class, 'destroy'])->name('equipment-part-groups.destroy');
Route::post('/equipment-part-groups/{equipmentPartGroup}/parts', [EquipmentPartGroupController::class, 'addPart'])->name('equipment-part-groups.add-part');
Route::delete('/equipment-part-groups/{equipmentPartGroup}/parts/{partId}', [EquipmentPartGroupController::class, 'removePart'])->name('equipment-part-groups.remove-part');

Route::get('/works', [WorkController::class, 'index'])->name('works.index');
Route::get('/works/create', [WorkController::class, 'create'])->name('works.create');
Route::post('/works', [WorkController::class, 'store'])->name('works.store');
Route::get('/works/{work}/edit', [WorkController::class, 'edit'])->name('works.edit');
Route::patch('/works/{work}', [WorkController::class, 'update'])->name('works.update');
Route::delete('/works/{work}', [WorkController::class, 'destroy'])->name('works.destroy');
Route::get('/works/history/{serial}', [WorkController::class, 'history'])->name('works.history');
Route::get('/works/{work}/print-preview', [WorkController::class, 'printPreview'])->name('works.print-preview');
Route::post('/works/preview-draft', [WorkController::class, 'previewDraft'])->name('works.preview-draft');

Route::get('/reports', [ReportController::class, 'index'])->name('reports.index');
Route::post('/reports/generate', [ReportController::class, 'generate'])->name('reports.generate');

Route::get('/acts', [ActController::class, 'index'])->name('acts.index');
Route::post('/acts', [ActController::class, 'store'])->name('acts.store');
Route::put('/acts/{act}', [ActController::class, 'update'])->name('acts.update');
Route::delete('/acts/{act}', [ActController::class, 'destroy'])->name('acts.destroy');
Route::get('/acts/archived-works', [ActController::class, 'getArchivedWorks'])->name('acts.archived-works');
Route::post('/acts/assign-work', [ActController::class, 'assignWork'])->name('acts.assign-work');
Route::post('/acts/remove-work', [ActController::class, 'removeWork'])->name('acts.remove-work');
Route::post('/acts/add-all-works', [ActController::class, 'addAllWorks'])->name('acts.add-all-works');
Route::post('/acts/remove-all-works', [ActController::class, 'removeAllWorks'])->name('acts.remove-all-works');
Route::post('/acts/update-exit-dates', [ActController::class, 'updateExitDates'])->name('acts.update-exit-dates');
Route::get('/acts/{act}/print', [ActController::class, 'printAct'])->name('acts.print');
Route::get('/acts/{act}/handover-pdf', [ActController::class, 'generateHandoverPdf'])->name('acts.handover-pdf');

Route::get('/reference', [ReferenceController::class, 'index'])->name('reference.index');
Route::get('/reference/print', [ReferenceController::class, 'print'])->name('reference.print');
Route::get('/reference/trilateral', [ReferenceController::class, 'trilateral'])->name('reference.trilateral');
Route::get('/reference/trilateral/word', [ReferenceController::class, 'trilateralWord'])->name('reference.trilateral.word');
Route::get('/reference/partners-by-period', [ReferenceController::class, 'getPartnersByPeriod'])->name('reference.partners-by-period');
Route::get('/reference/export-parts-used', [ReferenceController::class, 'exportPartsUsed'])->name('reference.export-parts-used');
Route::get('/reference/export-products-by-regions', [ReferenceController::class, 'exportProductsByRegions'])->name('reference.export-products-by-regions');
