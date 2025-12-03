<?php

namespace App\Http\Controllers;

use App\Models\Equipment;
use App\Models\Partner;
use App\Models\EquipmentStatus;
use App\Models\EquipmentStatusHistory;
use Illuminate\Http\Request;

class EquipmentController extends Controller
{

    public function store(Request $request)
    {
        $validated = $request->validate([
            'internal_id' => 'nullable|string|max:255',
            'name' => 'nullable|string|max:255',
            'stug_price' => 'nullable|string|max:255',
            'notes' => 'nullable|string',
        ]);

        Equipment::create($validated);

        if ($request->expectsJson()) {
            return response()->json(['success' => true]);
        }

        if ($request->has('redirect_to') && $request->redirect_to === 'counters') {
            return redirect()->route('general-data', 'counters')->with('success', 'Equipment added successfully.');
        }

        return redirect()->route('general-data', 'counters')->with('success', 'Equipment added successfully.');
    }

    public function update(Request $request, Equipment $equipment)
    {
        $validated = $request->validate([
            'internal_id' => 'nullable|string|max:255',
            'name' => 'nullable|string|max:255',
            'stug_price' => 'nullable|string|max:255',
            'notes' => 'nullable|string',
        ]);

        $equipment->update($validated);

        if ($equipment) {
            return response()->json(['success' => true]);
        }

        return redirect()->route('general-data', 'counters')->with('success', 'Equipment updated successfully.');
    }


    public function destroy(Equipment $equipment)
    {
        $equipment->delete();

        if (request()->expectsJson()) {
            return response()->json(['success' => true]);
        }

        return redirect()->route('general-data', 'counters')->with('success', 'Equipment deleted successfully.');
    }
}
