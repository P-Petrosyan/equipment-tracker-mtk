<?php

namespace App\Http\Controllers;

use App\Models\WorkOrder;
use App\Models\Equipment;
use Illuminate\Http\Request;

class WorkOrderController extends Controller
{
    public function index()
    {
        $workOrders = WorkOrder::with('equipment.partner')->latest()->paginate(20);
        return view('work_orders.index', compact('workOrders'));
    }

    public function create(Request $request)
    {
        $equipment = Equipment::findOrFail($request->equipment_id);
        return view('work_orders.create', compact('equipment'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'equipment_id' => 'required|exists:equipment,id',
            'description' => 'required|string',
            'type' => 'nullable|string',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date',
            'labor_cost' => 'nullable|numeric|min:0',
            'technician_name' => 'nullable|string',
        ]);

        WorkOrder::create($validated);

        return redirect()->route('equipment.show', $validated['equipment_id'])->with('success', 'Work record added.');
    }

    public function edit(WorkOrder $workOrder)
    {
        return view('work_orders.edit', compact('workOrder'));
    }

    public function update(Request $request, WorkOrder $workOrder)
    {
        $validated = $request->validate([
            'description' => 'required|string',
            'type' => 'nullable|string',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date',
            'labor_cost' => 'nullable|numeric|min:0',
            'technician_name' => 'nullable|string',
        ]);

        $workOrder->update($validated);

        return redirect()->route('equipment.show', $workOrder->equipment_id)->with('success', 'Work record updated.');
    }

    public function destroy(WorkOrder $workOrder)
    {
        $equipmentId = $workOrder->equipment_id;
        $workOrder->delete();
        return redirect()->route('equipment.show', $equipmentId)->with('success', 'Work record deleted.');
    }
}
