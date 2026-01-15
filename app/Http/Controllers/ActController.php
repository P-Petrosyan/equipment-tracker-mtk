<?php

namespace App\Http\Controllers;

use App\Models\Act;
use App\Models\Naming;
use App\Models\Partner;
use App\Models\Position;
use App\Models\Work;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;

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

    public function update(Request $request, Act $act)
    {
        $request->validate([
            'partner_id' => 'required|exists:partners,id',
            'act_date' => 'required|date',
            'act_number' => 'required|string|max:255'
        ]);

        $act->update($request->all());

        return response()->json(['success' => true]);
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
        $page = $request->get('page', 1);
        $search = $request->get('search', '');

        if (!$partnerId || !$actDate) {
            return response()->json([]);
        }

        $works = Work::with(['partner', 'equipment', 'partnerStructure', 'equipmentPartGroup'])
            ->where('partner_id', $partnerId)
            ->where('status', 1) // archived
            ->where('work_order_status', 0)
            ->whereDate('receive_date', '<=', $actDate)
            ->get();

        // Get assigned works for this act with pagination
        $assignedWorks = [];
        $pagination = null;
        if ($actId) {
            $perPage = 15;
            $assignedWorksQuery = Act::find($actId)->works()->with(['equipment', 'equipmentPartGroup'])->orderByPivot('id', 'desc');

            // Apply search filter if provided
            if (!empty($search)) {
                $assignedWorksQuery->where('new_serial_number', 'like', '%' . $search . '%');
            }

            $total = $assignedWorksQuery->count();
            $assignedWorks = $assignedWorksQuery->skip(($page - 1) * $perPage)->take($perPage)->get();

            $pagination = [
                'current_page' => (int)$page,
                'per_page' => $perPage,
                'total' => $total,
                'last_page' => ceil($total / $perPage)
            ];
        }
        return response()->json([
            'archived_works' => $works,
            'assigned_works' => $assignedWorks,
            'pagination' => $pagination
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

    public function addAllWorks(Request $request)
    {
        $actId = $request->act_id;
        $act = Act::find($actId);

        $works = Work::where('partner_id', $act->partner_id)
            ->where('status', 1)
            ->where('work_order_status', 0)
            ->whereDate('receive_date', '<=', $act->act_date)
            ->get();

        foreach ($works as $work) {
            if (!$act->works()->where('work_id', $work->id)->exists()) {
                $act->works()->attach($work->id);
                $work->update(['work_order_status' => 1]);
            }
        }

        return response()->json(['success' => true]);
    }

    public function removeAllWorks(Request $request)
    {
        $actId = $request->act_id;
        $act = Act::find($actId);

        $assignedWorks = $act->works;

        foreach ($assignedWorks as $work) {
            $act->works()->detach($work->id);
            $work->update(['work_order_status' => 0]);
        }

        return response()->json(['success' => true]);
    }

    public function updateExitDates(Request $request)
    {
        $actId = $request->act_id;
        $act = Act::find($actId);

        $act->works()->update(['exit_date' => now()->toDateString()]);

        return response()->json(['success' => true]);
    }

    public function printAct($id)
    {
        $act = Act::with(['partner', 'works.equipment', 'works.equipmentPartGroup'])->findOrFail($id);
        $tnoren = Position::where('title', 'Տնօրեն')->first();
        $naming = Naming::where('name', 'Պայմանագիր')->first();
        $repairedWorks = $act->works()->where('non_repairable', 0)->get();
        $nonRepairedWorks = $act->works()->where('non_repairable', 1)->get();
        $pdf = Pdf::loadView('acts.pdf', compact('act', 'tnoren', 'naming', 'repairedWorks', 'nonRepairedWorks'));
        $pdf->setPaper('A4', 'portrait');

        return $pdf->download($act->partner->region .' Կատ-ակտ Nº' . $act->act_number . ' - ' . now()->format('d.m.Y') . '.pdf');
    }

    public function generateHandoverPdf($id)
    {
        $act = Act::with(['partner', 'works.equipment'])->findOrFail($id);
        $tnoren = Position::where('title', 'Տնօրեն')->first();
        $laborant = Position::first(); // Լաբորատորիայի ղեկավար
        $pdf = Pdf::loadView('acts.handover-pdf', compact('act', 'tnoren', 'laborant'));
        $pdf->setPaper('A4', 'portrait');

        return $pdf->download($act->partner->region .' Հանձ-ընդ-ակտ Nº' . $act->act_number . ' - ' . now()->format('d.m.Y') . '.pdf');
    }


}
