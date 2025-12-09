<?php

namespace App\Http\Controllers;

use App\Models\Act;
use App\Models\Partner;
use App\Models\Work;
use Illuminate\Http\Request;

class ActController extends Controller
{
    public function index()
    {
        $acts = Act::with('partner')->orderBy('id', 'desc')->get();
        $partners = Partner::orderBy('region')->get();

        return view('acts.index', compact('acts', 'partners'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'partner_id' => 'required|exists:partners,id',
            'act_date' => 'required|date',
            'act_number' => 'required|string|max:255'
        ]);

        Act::create($request->all());

        return redirect()->route('acts.index')->with('success', 'Act created successfully');
    }

    public function destroy(Act $act)
    {
        $act->delete();
        return redirect()->route('acts.index')->with('success', 'Act deleted successfully');
    }

    public function getArchivedWorks(Request $request)
    {
        $partnerId = $request->get('partner_id');
        $actDate = $request->get('act_date');
        $actId = $request->get('act_id');

        if (!$partnerId || !$actDate) {
            return response()->json([]);
        }

        $works = Work::with(['partner', 'equipment', 'partnerStructure', 'equipmentPartGroup'])
            ->where('partner_id', $partnerId)
            ->where('status', 1) // archived
            ->where('work_order_status', 0)
            ->whereDate('receive_date', '<=', $actDate)
            ->get();

        // Get assigned works for this act
        $assignedWorks = [];
        if ($actId) {
            $assignedWorks = Act::find($actId)->works()->with(['equipment', 'equipmentPartGroup'])->get();
        }

        return response()->json([
            'archived_works' => $works,
            'assigned_works' => $assignedWorks
        ]);
    }

    public function assignWork(Request $request)
    {
        $actId = $request->act_id;
        $workId = $request->work_id;

        $act = Act::find($actId);
        $work = Work::find($workId);

        if (!$act->works()->where('work_id', $workId)->exists()) {
            $act->works()->attach($workId);
            $work->update(['work_order_status' => 1]);
        }

        return response()->json(['success' => true]);
    }

    public function removeWork(Request $request)
    {
        $actId = $request->act_id;
        $workId = $request->work_id;

        $act = Act::find($actId);
        $work = Work::find($workId);

        $act->works()->detach($workId);
        $work->update(['work_order_status' => 0]);

        return response()->json(['success' => true]);
    }
}
