<?php

namespace App\Http\Controllers;

use App\Models\Part;
use App\Models\PartsSnapshot;
use App\Exports\PartsExport;
use App\Exports\SnapshotExport;
use App\Imports\PartsImport;
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
            'used_quantity' => 'nullable|integer|min:0',
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
            'used_quantity' => 'nullable|integer|min:0',
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

    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,xls'
        ]);

        Excel::import(new PartsImport, $request->file('file'));

        return redirect()->route('general-data', 'parts')->with('success', 'Parts imported successfully.');
    }

    public function importQuantities(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,xls'
        ]);

        Excel::import(new \App\Imports\PartsQuantityImport, $request->file('file'));

        return redirect()->route('parts.index')->with('success', 'Quantities imported successfully.');
    }

    public function createSnapshot(Request $request)
    {
        $request->validate([
            'snapshot_date' => 'required|date|unique:parts_snapshots,snapshot_date',
            'snapshot_comment' => 'nullable|string|max:1000'
        ]);

        $partsData = Part::all()->toArray();

        PartsSnapshot::create([
            'snapshot_date' => $request->snapshot_date,
            'snapshot_comment' => $request->snapshot_comment,
            'parts_data' => $partsData
        ]);

        // Reset parts table
        Part::query()->update([
            'quantity' => 0,
            'used_quantity' => 0
        ]);

        return redirect()->route('general-data', 'parts')->with('success', 'Snapshot created and parts reset successfully.');
    }

    public function getSnapshots()
    {
        $snapshots = PartsSnapshot::orderBy('snapshot_date', 'desc')->get();
        return response()->json($snapshots);
    }

    public function viewSnapshot($date)
    {
        $snapshot = PartsSnapshot::where('snapshot_date', $date)->first();
        if (!$snapshot) {
            return redirect()->route('general-data', 'parts')->with('error', 'Snapshot not found.');
        }

        $parts = collect($snapshot->parts_data);
        return view('parts.snapshot', compact('parts', 'date'));
    }

    public function getSnapshotData($date)
    {
        $snapshot = PartsSnapshot::where('snapshot_date', $date)->first();
        if (!$snapshot) {
            return response()->json(['error' => 'Snapshot not found'], 404);
        }

        return response()->json($snapshot);
    }

    public function exportSnapshot($date)
    {
        $snapshot = PartsSnapshot::where('snapshot_date', $date)->first();
        if (!$snapshot) {
            return redirect()->back()->with('error', 'Snapshot not found.');
        }

        $filename = 'snapshot_' . $date . '.xlsx';
        return Excel::download(new SnapshotExport($snapshot->parts_data, $snapshot->snapshot_date, $snapshot->snapshot_comment), $filename);
    }
}
