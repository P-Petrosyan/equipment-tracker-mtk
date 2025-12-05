<?php

namespace App\Http\Controllers;

use App\Models\Defect;
use Illuminate\Http\Request;

class DefectController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'group_id' => 'nullable|string|max:255',
            'description' => 'required|string',
            'note' => 'nullable|string',
        ]);

        Defect::create($validated);

        return redirect()->route('general-data', 'defects')->with('success', 'Defect added successfully.');
    }

    public function update(Request $request, Defect $defect)
    {
        $validated = $request->validate([
            'group_id' => 'nullable|string|max:255',
            'description' => 'required|string',
            'note' => 'nullable|string',
        ]);

        $defect->update($validated);

        if ($defect) {
            return response()->json(['success' => true]);
        }

        return redirect()->route('general-data', 'defects')->with('success', 'Defect updated successfully.');
    }

    public function destroy(Defect $defect)
    {
        $defect->delete();
        return redirect()->route('general-data', 'defects')->with('success', 'Defect deleted successfully.');
    }
}
