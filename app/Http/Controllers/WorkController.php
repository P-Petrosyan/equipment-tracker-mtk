<?php

namespace App\Http\Controllers;

use App\Models\Work;
use Illuminate\Http\Request;

class WorkController extends Controller
{
    public function index(Request $request)
    {
        $activeQuery = Work::with(['partner', 'partnerStructure', 'equipment', 'equipmentPartGroup'])
            ->where('status', 0);
        
        $archivedQuery = Work::with(['partner', 'partnerStructure', 'equipment', 'equipmentPartGroup'])
            ->where('status', 1);

        if ($request->filled('old_serial')) {
            $activeQuery->where('old_serial_number', 'like', '%' . $request->old_serial . '%');
            $archivedQuery->where('old_serial_number', 'like', '%' . $request->old_serial . '%');
        }

        if ($request->filled('new_serial')) {
            $activeQuery->where('new_serial_number', 'like', '%' . $request->new_serial . '%');
            $archivedQuery->where('new_serial_number', 'like', '%' . $request->new_serial . '%');
        }

        $activeWorks = $activeQuery->paginate(15, ['*'], 'active');
        $archivedWorks = $archivedQuery->paginate(15, ['*'], 'archived');
        
        return view('works.index', compact('activeWorks', 'archivedWorks'));
    }

    public function create()
    {
        $partners = \App\Models\Partner::with('structures')->get();
        $equipment = \App\Models\Equipment::with('partGroups')->get();
        $defects = \App\Models\Defect::all();
        $nextConclusionNumber = $this->getNextConclusionNumber();
        return view('works.create', compact('partners', 'equipment', 'defects', 'nextConclusionNumber'));
    }

    public function edit(Work $work)
    {
        $partners = \App\Models\Partner::with('structures')->get();
        $equipment = \App\Models\Equipment::with('partGroups')->get();
        $defects = \App\Models\Defect::all();
        $nextConclusionNumber = $this->getNextConclusionNumber();
        return view('works.edit', compact('work', 'partners', 'equipment', 'defects', 'nextConclusionNumber'));
    }
    public function store(Request $request)
    {
        $validated = $request->validate([
            'receive_date' => 'required|date',
            'exit_date' => 'nullable|date',
            'partner_id' => 'required|exists:partners,id',
            'partner_structure_id' => 'nullable|exists:partner_structures,id',
            'equipment_id' => 'required|exists:equipment,id',
            'equipment_part_group_id' => 'nullable|exists:equipment_part_groups,id',
            'equipment_part_group_total_price' => 'nullable|numeric|min:0',
            'old_serial_number' => 'nullable|string|max:255',
            'new_serial_number' => 'nullable|string|max:255',
            'partner_representative' => 'nullable|string|max:255',
            'non_repairable' => 'nullable|boolean',
            'conclusion_number' => 'nullable|string|max:255',
            'defects_description' => 'nullable|string',
            'work_order_status' => 'nullable|in:0,1',
            'status' => 'required|in:0,1'
        ]);

        Work::create($validated);

        return redirect()->route('works.index')->with('success', 'Work added successfully.');
    }

    public function update(Request $request, Work $work)
    {
        $validated = $request->validate([
            'receive_date' => 'sometimes|required|date',
            'exit_date' => 'nullable|date',
            'partner_id' => 'sometimes|required|exists:partners,id',
            'partner_structure_id' => 'nullable|exists:partner_structures,id',
            'equipment_id' => 'sometimes|required|exists:equipment,id',
            'equipment_part_group_id' => 'nullable|exists:equipment_part_groups,id',
            'equipment_part_group_total_price' => 'nullable|numeric|min:0',
            'old_serial_number' => 'nullable|string|max:255',
            'new_serial_number' => 'nullable|string|max:255',
            'partner_representative' => 'nullable|string|max:255',
            'non_repairable' => 'nullable|boolean',
            'conclusion_number' => 'nullable|string|max:255',
            'defects_description' => 'nullable|string',
            'work_order_status' => 'nullable|in:0,1',
            'status' => 'sometimes|required|in:0,1',
            'work_order_status' => 'sometimes|required|in:0,1',
        ]);

        $work->update($validated);

        return redirect()->route('works.index')->with('success', 'Work updated successfully.');
    }

    public function destroy(Work $work)
    {
        $work->delete();
        return redirect()->route('works.index')->with('success', 'Work deleted successfully.');
    }

    public function history($serial)
    {
        $works = Work::with(['partner', 'partnerStructure', 'equipment', 'equipmentPartGroup'])
            ->where('old_serial_number', $serial)
            ->orderBy('receive_date', 'desc')
            ->get();
        
        return view('works.history', compact('works', 'serial'));
    }

    public function printPreview(Work $work)
    {
        $position = \App\Models\Position::first();
        return view('works.print-preview', compact('work', 'position'));
    }

    private function getNextConclusionNumber()
    {
        $lastWork = Work::whereNotNull('conclusion_number')
            ->where('non_repairable', true)
            ->orderBy('conclusion_number', 'desc')
            ->first();

        return $lastWork ? (int)$lastWork->conclusion_number + 1 : 1;
    }
}
