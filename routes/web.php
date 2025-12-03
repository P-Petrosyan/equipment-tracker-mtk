<?php

use App\Http\Controllers\PartnerController;
use App\Http\Controllers\EquipmentController;
use App\Http\Controllers\WorkOrderController;
use App\Http\Controllers\PartController;
use App\Http\Controllers\ReportController;
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

Route::post('/partner-structures', [\App\Http\Controllers\PartnerStructureController::class, 'store'])->name('partner-structures.store');
Route::delete('/partner-structures/{partnerStructure}', [\App\Http\Controllers\PartnerStructureController::class, 'destroy'])->name('partner-structures.destroy');

Route::post('/reasons', [\App\Http\Controllers\ReasonController::class, 'store'])->name('reasons.store');
Route::delete('/reasons/{reason}', [\App\Http\Controllers\ReasonController::class, 'destroy'])->name('reasons.destroy');

Route::post('/defects', [\App\Http\Controllers\DefectController::class, 'store'])->name('defects.store');
Route::delete('/defects/{defect}', [\App\Http\Controllers\DefectController::class, 'destroy'])->name('defects.destroy');

Route::post('/positions', [\App\Http\Controllers\PositionController::class, 'store'])->name('positions.store');
Route::delete('/positions/{position}', [\App\Http\Controllers\PositionController::class, 'destroy'])->name('positions.destroy');

Route::post('/namings', [\App\Http\Controllers\NamingController::class, 'store'])->name('namings.store');
Route::delete('/namings/{naming}', [\App\Http\Controllers\NamingController::class, 'destroy'])->name('namings.destroy');

Route::get('/parts', [PartController::class, 'index'])->name('parts.index');
Route::post('/parts', [PartController::class, 'store'])->name('parts.store');
Route::patch('/parts/{part}', [PartController::class, 'update'])->name('parts.update');
Route::delete('/parts/{part}', [PartController::class, 'destroy'])->name('parts.destroy');
Route::post('/parts/{part}/add-quantity', [PartController::class, 'addQuantity'])->name('parts.add-quantity');

Route::post('/equipment-part-groups', [\App\Http\Controllers\EquipmentPartGroupController::class, 'store'])->name('equipment-part-groups.store');
Route::patch('/equipment-part-groups/{equipmentPartGroup}', [\App\Http\Controllers\EquipmentPartGroupController::class, 'update'])->name('equipment-part-groups.update');
Route::delete('/equipment-part-groups/{equipmentPartGroup}', [\App\Http\Controllers\EquipmentPartGroupController::class, 'destroy'])->name('equipment-part-groups.destroy');
Route::post('/equipment-part-groups/{equipmentPartGroup}/parts', [\App\Http\Controllers\EquipmentPartGroupController::class, 'addPart'])->name('equipment-part-groups.add-part');
Route::delete('/equipment-part-groups/{equipmentPartGroup}/parts/{partId}', [\App\Http\Controllers\EquipmentPartGroupController::class, 'removePart'])->name('equipment-part-groups.remove-part');

Route::get('/reports', [ReportController::class, 'index'])->name('reports.index');
Route::post('/reports/generate', [ReportController::class, 'generate'])->name('reports.generate');
