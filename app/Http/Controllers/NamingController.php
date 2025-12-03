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

    public function destroy(Naming $naming)
    {
        $naming->delete();
        return redirect()->route('general-data', 'namings')->with('success', 'Naming deleted successfully.');
    }
}
