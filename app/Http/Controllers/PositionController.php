<?php

namespace App\Http\Controllers;

use App\Models\Position;
use Illuminate\Http\Request;

class PositionController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'titleholder' => 'nullable|string|max:255',
            'note' => 'nullable|string',
        ]);

        Position::create($validated);

        return redirect()->route('general-data', 'positions')->with('success', 'Position added successfully.');
    }

    public function update(Request $request, Position $position)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'titleholder' => 'nullable|string|max:255',
            'note' => 'nullable|string',
        ]);

        $position->update($validated);

        if ($position) {
            return response()->json(['success' => true]);
        }

        return redirect()->route('general-data', 'positions')->with('success', 'Position updated successfully.');
    }

    public function destroy(Position $position)
    {
        $position->delete();
        return redirect()->route('general-data', 'positions')->with('success', 'Position deleted successfully.');
    }
}
