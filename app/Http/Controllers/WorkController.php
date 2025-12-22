<?php

namespace App\Http\Controllers;

use App\Models\Partner;
use App\Models\Work;
use App\Models\EquipmentPartGroup;
use App\Models\Equipment;
use App\Models\Defect;
use App\Models\Position;
use Illuminate\Http\Request;

class WorkController extends Controller
{
    public function index(Request $request)
    {
        $currentTable = $request->get('table', 'active');

        $activeQuery = Work::with(['partner', 'partnerStructure', 'equipment', 'equipmentPartGroup'])
            ->where('status', 0);

        $archivedQuery = Work::with(['partner', 'partnerStructure', 'equipment', 'equipmentPartGroup'])
            ->where('status', 1);

        // Apply search filters only to the current table
        if ($currentTable === 'active') {
            if ($request->filled('old_serial')) {
                $activeQuery->where('old_serial_number', 'like', '%' . $request->old_serial . '%');
            }
            if ($request->filled('new_serial')) {
                $activeQuery->where('new_serial_number', 'like', '%' . $request->new_serial . '%');
            }
        } else {
            if ($request->filled('old_serial')) {
                $archivedQuery->where('old_serial_number', 'like', '%' . $request->old_serial . '%');
            }
            if ($request->filled('new_serial')) {
                $archivedQuery->where('new_serial_number', 'like', '%' . $request->new_serial . '%');
            }
        }

        $activeWorks = $activeQuery->paginate(15, ['*'], 'active');
        $archivedWorks = $archivedQuery->paginate(15, ['*'], 'archived');

        return view('works.index', compact('activeWorks', 'archivedWorks', 'currentTable'));
    }

    public function create()
    {
        $partners = Partner::with('structures')->get();
        $equipment = Equipment::with('partGroups')->get();
        $defects = Defect::all();
        $nextConclusionNumber = $this->getNextConclusionNumber();
        return view('works.create', compact('partners', 'equipment', 'defects', 'nextConclusionNumber'));
    }

    public function edit(Work $work)
    {
        $work->load('acts.partner');
        $partners = Partner::with('structures')->get();
        $equipment = Equipment::with('partGroups')->get();
        $defects = Defect::all();
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

        if ($validated['equipment_part_group_id']) {
            $group = EquipmentPartGroup::with('parts')->find($validated['equipment_part_group_id']);
            if ($group) {
                // Validate part availability first
                $insufficientParts = $this->validatePartAvailability($group);
                if ($insufficientParts) {
                    return back()->withErrors(['parts_error' => $insufficientParts])->withInput();
                }

                if (is_numeric($group->notes) && $group->notes > 0) {
                    $group->decrement('notes');

                    // Update parts quantities
                    foreach ($group->parts as $part) {
                        $pivotQuantity = $part->pivot->quantity;
                        $part->decrement('quantity', $pivotQuantity);
                        $part->increment('used_quantity', $pivotQuantity);
                    }
                }
            }
        }

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
        ]);

        $oldGroupId = $work->equipment_part_group_id;
        $newGroupId = $validated['equipment_part_group_id'];

        // Validate new group BEFORE making any changes
        if ($oldGroupId != $newGroupId && $newGroupId) {
            $newGroup = EquipmentPartGroup::with('parts')->find($newGroupId);
            if ($newGroup) {
                $insufficientParts = $this->validatePartAvailability($newGroup);
                if ($insufficientParts) {
                    return back()->withErrors(['parts_error' => $insufficientParts])->withInput();
                }
            }
        }

        // Now proceed with database changes since validation passed
        if ($oldGroupId != $newGroupId) {
            if ($oldGroupId) {
                $oldGroup = EquipmentPartGroup::with('parts')->find($oldGroupId);
                if ($oldGroup && is_numeric($oldGroup->notes)) {
                    $oldGroup->increment('notes');

                    // Restore parts quantities
                    foreach ($oldGroup->parts as $part) {
                        $pivotQuantity = $part->pivot->quantity;
                        $part->increment('quantity', $pivotQuantity);
                        $part->decrement('used_quantity', $pivotQuantity);
                    }
                }
            }
            if ($newGroupId) {
                $newGroup = EquipmentPartGroup::with('parts')->find($newGroupId);
                if ($newGroup && is_numeric($newGroup->notes) && $newGroup->notes > 0) {
                    $newGroup->decrement('notes');

                    // Update parts quantities
                    foreach ($newGroup->parts as $part) {
                        $pivotQuantity = $part->pivot->quantity;
                        $part->decrement('quantity', $pivotQuantity);
                        $part->increment('used_quantity', $pivotQuantity);
                    }
                }
            }
        }

        // Check if trying to change from archived to active when work has acts
        if ($work->status == 1 && isset($validated['status']) && $validated['status'] == 0) {
            if ($work->work_order_status == 1 && $work->acts()->exists()) {
                $acts = $work->acts()->with('partner')->get();
                $actsList = $acts->map(function($act) {
                    return "Ակտ #{$act->act_number} ({$act->partner->region}) - {$act->act_date->format('d.m.Y')}";
                })->implode(', ');

                return back()->withErrors([
                    'act_error' => "Այս աշխատանքը կապված է հետևյալ ակտ(եր)ի հետ: {$actsList}. Նախ հեռացրեք աշխատանքը ակտից:"
                ])->withInput();
            }
            $validated['work_order_status'] = 0;
        }

        // If work_order_status is being set to 0, remove from all acts
        if (isset($validated['work_order_status']) && $validated['work_order_status'] == 0) {
            $work->acts()->detach();
        }

        $work->update($validated);

        $table = $request->input('original_table', $work->status == 1 ? 'archived' : 'active');
        return redirect()->route('works.index', ['table' => $table])->with('success', 'Work updated successfully.');
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
        $position = Position::first();
        return view('works.print-preview', compact('work', 'position'));
    }

    public function previewDraft(Request $request)
    {
        $equipment = Equipment::find($request->equipment_id);

        $work = (object) [
            'conclusion_number' => $request->conclusion_number,
            'old_serial_number' => $request->old_serial_number,
            'equipment' => $equipment,
        ];

        $position = Position::first();
        return view('works.print-preview', compact('work', 'position'));
    }

    private function validatePartAvailability($group)
    {
        $insufficientParts = [];

        foreach ($group->parts as $part) {
            $requiredQuantity = $part->pivot->quantity;
            $availableQuantity = $part->quantity ?? 0;

            if ($availableQuantity < $requiredQuantity) {
                $insufficientParts[] = " {$part->code} ({$part->name}): Անհրաժեշտ {$requiredQuantity}, առկա {$availableQuantity}";
            }
        }

        if (!empty($insufficientParts)) {
            return 'Անբավարար քանակ: ' . implode('; ', $insufficientParts);
        }

        return null;
    }

    private function getNextConclusionNumber()
    {
        $lastWork = Work::whereNotNull('conclusion_number')
            ->where('non_repairable', true)
            ->orderByRaw('CAST(conclusion_number AS UNSIGNED) DESC')
            ->first();

        return $lastWork ? (int)$lastWork->conclusion_number + 1 : 1;
    }
}
