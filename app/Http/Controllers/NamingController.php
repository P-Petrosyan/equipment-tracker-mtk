<?php

namespace App\Http\Controllers;

use App\Models\Naming;
use Illuminate\Http\Request;

class NamingController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'text' => 'nullable|string',
        ]);

        Naming::create($validated);

        return redirect()->route('general-data', 'namings')->with('success', 'Naming added successfully.');
    }

    public function update(Request $request, Naming $naming)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'text' => 'nullable|string',
        ]);

        $naming->update($validated);

        if ($naming) {
            return response()->json(['success' => true]);
        }

        return redirect()->route('general-data', 'namings')->with('success', 'Naming updated successfully.');
    }

    public function destroy(Naming $naming)
    {
        $naming->delete();
        return redirect()->route('general-data', 'namings')->with('success', 'Naming deleted successfully.');
    }
}
