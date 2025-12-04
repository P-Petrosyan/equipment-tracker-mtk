<?php

namespace App\Http\Controllers;

use App\Models\Partner;
use Illuminate\Http\Request;

class PartnerController extends Controller
{
    public function index(Request $request)
    {
        $query = Partner::query();

        if ($request->has('search')) {
            $search = $request->get('search');
            $query->where('tnoren', 'like', "%{$search}%")
                ->orWhere('region', 'like', "%{$search}%")
                ->orWhere('address', 'like', "%{$search}%");
        }

        $partners = $query->latest()->paginate(10);

        return view('partners.index', compact('partners'));
    }

    public function create()
    {
        return view('partners.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'region' => 'required|string|max:255',
            'address' => 'nullable|string',
            'tnoren' => 'nullable|string|max:255',
            'hashvapah' => 'nullable|string|max:255',
            'notes' => 'nullable|string',
        ]);

        Partner::create($validated);

        if ($request->has('redirect_to') && $request->redirect_to === 'partners') {
            return redirect()->route('general-data', 'partners')->with('success', 'Partner created successfully.');
        }

        return redirect()->route('general-data', 'partners')->with('success', 'Partner created successfully.');
    }

    public function show(Partner $partner)
    {
        $partner->load(['equipment.status']);
        return view('partners.show', compact('partner'));
    }

    public function edit(Partner $partner)
    {
        return view('partners.edit', compact('partner'));
    }

    public function update(Request $request, Partner $partner)
    {
        $validated = $request->validate([
            'region' => 'sometimes|required|string|max:255',
            'address' => 'nullable|string',
            'tnoren' => 'nullable|string|max:255',
            'hashvapah' => 'nullable|string|max:255',
            'notes' => 'nullable|string',
        ]);

        $partner->update($validated);


        if ($partner) {
            return response()->json(['message' => 'Partner updated successfully', 'partner' => $partner]);
        }

        return redirect()->route('general-data', 'partners')->with('success', 'Partner updated successfully.');
    }

    public function destroy(Partner $partner)
    {
        $partner->delete();
        return redirect()->route('general-data', 'partners')->with('success', 'Partner deleted successfully.');
    }

    public function updateFromInput(Request $request, Partner $partner)
    {
        $validated = $request->validate([
            'region' => 'sometimes|required|string|max:255',
            'address' => 'nullable|string',
            'tnoren' => 'nullable|string|max:255',
            'hashvapah' => 'nullable|string|max:255',
            'notes' => 'nullable|string',
        ]);

        $partner->update($validated);

        if ($request->wantsJson()) {
            return response()->json(['message' => 'Partner updated successfully', 'partner' => $partner]);
        }

        return redirect()->route('general-data', 'partners')->with('success', 'Partner updated successfully.');
    }
}
