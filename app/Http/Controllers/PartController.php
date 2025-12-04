<?php

namespace App\Http\Controllers;

use App\Models\Part;
use App\Exports\PartsExport;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class PartController extends Controller
{
    public function index()
    {
        $parts = Part::all();
        return view('parts.index', compact('parts'));
    }
    public function store(Request $request)
    {
        $validated = $request->validate([
            'code' => 'required|string|max:255',
            'name' => 'required|string|max:255',
            'unit_price' => 'required|numeric|min:0',
            'quantity' => 'nullable|integer|min:1',
            'measure_unit' => 'string|max:255',
        ]);

        Part::create($validated);

        return redirect()->route('general-data', 'parts')->with('success', 'part added successfully.');
    }

    public function update(Request $request, Part $part)
    {
        $validated = $request->validate([
            'code' => 'sometimes|required|string|max:255',
            'name' => 'sometimes|required|string|max:255',
            'unit_price' => 'sometimes|required|numeric|min:0',
            'quantity' => 'nullable|integer|min:1',
            'measure_unit' => 'sometimes|required|string|max:255',
        ]);

        $part->update($validated);

        if ($part) {
            return response()->json(['message' => 'part updated successfully', 'part' => $part]);
        }

        return redirect()->route('general-data', 'parts')->with('success', 'part updated successfully.');
    }

    public function destroy(Part $part)
    {
        $part->delete();
        return redirect()->route('general-data', 'parts')->with('success', 'part deleted successfully.');
    }

    public function addQuantity(Request $request, Part $part)
    {
        $request->validate([
            'add_quantity' => 'required|numeric|min:0.01'
        ]);

        $currentQuantity = $part->quantity ?? 0;
        $newQuantity = $currentQuantity + $request->add_quantity;

        $part->update(['quantity' => $newQuantity]);

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'new_quantity' => $newQuantity,
                'added_quantity' => $request->add_quantity
            ]);
        }

        return redirect()->back()->with('success', 'Quantity added successfully.');
    }

    public function export()
    {
        return Excel::download(new PartsExport, 'parts_export.xlsx');
    }
}
