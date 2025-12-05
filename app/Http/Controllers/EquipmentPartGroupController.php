<?php

namespace App\Http\Controllers;

use App\Models\EquipmentPartGroup;
use Illuminate\Http\Request;

class EquipmentPartGroupController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'equipment_id' => 'required|exists:equipment,id',
            'name' => 'required|string|max:255',
            'notes' => 'nullable|string'
        ]);

        EquipmentPartGroup::create($request->all());
        return response()->json(['success' => true]);
    }

    public function update(Request $request, EquipmentPartGroup $equipmentPartGroup)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'notes' => 'nullable|string'
        ]);

        $equipmentPartGroup->update($request->all());
        return response()->json(['success' => true]);
    }

    public function destroy(EquipmentPartGroup $equipmentPartGroup)
    {
        $equipmentPartGroup->delete();
        return response()->json(['success' => true]);
    }

    public function addPart(Request $request, EquipmentPartGroup $equipmentPartGroup)
    {
        $request->validate([
            'part_id' => 'required|exists:parts,id',
            'quantity' => 'required|numeric|min:1',
            'unit_price' => 'nullable|numeric|min:0'
        ]);

        $equipmentPartGroup->parts()->attach($request->part_id, [
            'quantity' => $request->quantity,
        ]);

        $this->updateGroupTotalPrice($equipmentPartGroup);

        return response()->json(['success' => true]);
    }

    public function removePart(EquipmentPartGroup $equipmentPartGroup, $partId)
    {
        $equipmentPartGroup->parts()->detach($partId);
        $this->updateGroupTotalPrice($equipmentPartGroup);
        return response()->json(['success' => true]);
    }

    private function updateGroupTotalPrice(EquipmentPartGroup $equipmentPartGroup)
    {
        $totalPrice = $equipmentPartGroup->parts()->withPivot('quantity')
            ->get()
            ->sum(function ($part) {
                return $part->unit_price * $part->pivot->quantity;
            });

        $equipmentPartGroup->update(['total_price' => $totalPrice]);
    }
}
