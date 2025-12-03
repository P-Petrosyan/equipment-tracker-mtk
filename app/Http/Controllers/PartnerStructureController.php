<?php

namespace App\Http\Controllers;

use App\Models\PartnerStructure;
use Illuminate\Http\Request;

class PartnerStructureController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'partner_id' => 'required|exists:partners,id',
            'name' => 'required|string|max:255',
        ]);

        PartnerStructure::create($validated);

        if ($request->expectsJson()) {
            return response()->json(['success' => true]);
        }

        return redirect()->route('general-data', 'partners')->with('success', 'Structure added successfully.');
    }

    public function destroy(PartnerStructure $partnerStructure)
    {
        $partnerStructure->delete();
        
        if (request()->expectsJson()) {
            return response()->json(['success' => true]);
        }
        
        return redirect()->route('general-data', 'partners')->with('success', 'Structure deleted successfully.');
    }
}
