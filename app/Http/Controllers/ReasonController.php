<?php

namespace App\Http\Controllers;

use App\Models\Reason;
use Illuminate\Http\Request;

class ReasonController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
        ]);

        Reason::create($validated);

        return redirect()->route('general-data', 'reasons')->with('success', 'Reason added successfully.');
    }

    public function update(Request $request, Reason $reason)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
        ]);

        $reason->update($validated);

        if ($reason) {
            return response()->json(['success' => true]);
        }

        return redirect()->route('general-data', 'reasons')->with('success', 'Reason updated successfully.');
    }

    public function destroy(Reason $reason)
    {
        $reason->delete();
        return redirect()->route('general-data', 'reasons')->with('success', 'Reason deleted successfully.');
    }
}
