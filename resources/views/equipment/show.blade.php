@extends('layouts.app')

@section('title', 'Equipment Details')

@section('content')
    <div class="header">
        <h1 class="page-title">Equipment: {{ $equipment->model }}</h1>
        <div class="flex gap-2">
            <a href="{{ route('equipment.edit', $equipment) }}" class="btn btn-secondary">
                <i class="fa-solid fa-pen"></i> Edit
            </a>
            <a href="{{ route('equipment.index') }}" class="btn btn-secondary">
                <i class="fa-solid fa-arrow-left"></i> Back
            </a>
        </div>
    </div>

    <div class="grid grid-cols-2 gap-4 mb-4">
        <div class="card">
            <h3 class="text-muted mb-4">Details</h3>
            <p><strong>Internal ID:</strong> {{ $equipment->internal_id ?? 'N/A' }}</p>
            <p><strong>Serial Number:</strong> {{ $equipment->serial_number ?? 'N/A' }}</p>
            <p><strong>Type:</strong> {{ $equipment->type ?? 'N/A' }}</p>
            <p><strong>Partner:</strong> <a href="{{ route('partners.show', $equipment->partner) }}">{{ $equipment->partner->name }}</a></p>
            <p><strong>Received:</strong> {{ $equipment->received_at ? $equipment->received_at->format('Y-m-d') : 'N/A' }}</p>
            
            <div id="status-display" class="flex items-center gap-2">
                <strong>Current Status:</strong> 
                <span class="badge badge-{{ $equipment->status->color }}">{{ $equipment->status->name }}</span>
                <button onclick="toggleStatusEdit()" class="btn btn-sm btn-secondary" title="Update Status">
                    <i class="fa-solid fa-pen"></i>
                </button>
            </div>

            <form id="status-form" action="{{ route('equipment.update-status', $equipment) }}" method="POST" style="display: none; margin-top: 0.5rem; background: #f8fafc; padding: 1rem; border-radius: 0.5rem; border: 1px solid var(--border-color);">
                @csrf
                @method('PATCH')
                <div class="form-group">
                    <label class="form-label">New Status</label>
                    <select name="status_id" class="form-control">
                        @foreach($statuses as $status)
                            <option value="{{ $status->id }}" {{ $equipment->status_id == $status->id ? 'selected' : '' }}>
                                {{ $status->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group">
                    <label class="form-label">Note (Optional)</label>
                    <input type="text" name="notes" class="form-control" placeholder="Reason for change...">
                </div>
                <div class="flex gap-2">
                    <button type="submit" class="btn btn-primary btn-sm">Save</button>
                    <button type="button" onclick="toggleStatusEdit()" class="btn btn-secondary btn-sm">Cancel</button>
                </div>
            </form>

            <script>
                function toggleStatusEdit() {
                    const display = document.getElementById('status-display');
                    const form = document.getElementById('status-form');
                    if (form.style.display === 'none') {
                        form.style.display = 'block';
                        display.style.display = 'none';
                    } else {
                        form.style.display = 'none';
                        display.style.display = 'flex';
                    }
                }
            </script>
        </div>
        <div class="card">
            <h3 class="text-muted mb-4">Description & Notes</h3>
            <p><strong>Problem:</strong> {{ $equipment->description ?? 'N/A' }}</p>
            <hr style="margin: 1rem 0; border: 0; border-top: 1px solid var(--border-color);">
            <p><strong>Notes:</strong> {{ $equipment->notes ?? 'N/A' }}</p>
        </div>
    </div>

    <div class="card mb-4">
        <div class="flex justify-between items-center mb-4">
            <h2>Work / Repair Jobs</h2>
            <a href="{{ route('work-orders.create', ['equipment_id' => $equipment->id]) }}" class="btn btn-primary btn-sm">
                <i class="fa-solid fa-plus"></i> Add Work
            </a>
        </div>
        
        <div class="table-container">
            <table class="table">
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Description</th>
                        <th>Technician</th>
                        <th>Labor Cost</th>
                        <th>Parts Cost</th>
                        <th>Total</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @php $totalLabor = 0; $totalParts = 0; @endphp
                    @forelse($equipment->workOrders as $work)
                        @php 
                            $partsCost = $work->parts->sum('total_price');
                            $totalLabor += $work->labor_cost;
                            $totalParts += $partsCost;
                        @endphp
                        <tr>
                            <td>{{ $work->end_date ? $work->end_date->format('Y-m-d') : ($work->start_date ? $work->start_date->format('Y-m-d') . ' (Start)' : '-') }}</td>
                            <td>{{ $work->description }}</td>
                            <td>{{ $work->technician_name ?? '-' }}</td>
                            <td>{{ number_format($work->labor_cost, 2) }}</td>
                            <td>{{ number_format($partsCost, 2) }}</td>
                            <td><strong>{{ number_format($work->labor_cost + $partsCost, 2) }}</strong></td>
                            <td class="flex gap-2">
                                <a href="{{ route('work-orders.edit', $work) }}" class="btn btn-sm btn-secondary"><i class="fa-solid fa-pen"></i></a>
                                <form action="{{ route('work-orders.destroy', $work) }}" method="POST" onsubmit="return confirm('Delete this work record?')">
                                    @csrf @method('DELETE')
                                    <button class="btn btn-sm btn-danger"><i class="fa-solid fa-trash"></i></button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="7" class="text-center text-muted">No work records.</td></tr>
                    @endforelse
                </tbody>
                <tfoot>
                    <tr style="background-color: #f8fafc; font-weight: bold;">
                        <td colspan="3" class="text-right">Totals:</td>
                        <td>{{ number_format($totalLabor, 2) }}</td>
                        <td>{{ number_format($totalParts, 2) }}</td>
                        <td>{{ number_format($totalLabor + $totalParts, 2) }}</td>
                        <td></td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>

    <div class="card mb-4">
        <div class="flex justify-between items-center mb-4">
            <h2>Parts Used (Directly on Equipment)</h2>
            <a href="{{ route('parts.create', ['equipment_id' => $equipment->id]) }}" class="btn btn-primary btn-sm">
                <i class="fa-solid fa-plus"></i> Add Part
            </a>
        </div>
        
        <div class="table-container">
            <table class="table">
                <thead>
                    <tr>
                        <th>Part Name</th>
                        <th>Code</th>
                        <th>Qty</th>
                        <th>Unit Price</th>
                        <th>Total</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @php $directPartsTotal = 0; @endphp
                    @forelse($equipment->parts->whereNull('work_order_id') as $part)
                        @php $directPartsTotal += $part->total_price; @endphp
                        <tr>
                            <td>{{ $part->name }}</td>
                            <td>{{ $part->code ?? '-' }}</td>
                            <td>{{ $part->quantity }}</td>
                            <td>{{ number_format($part->unit_price, 2) }}</td>
                            <td>{{ number_format($part->total_price, 2) }}</td>
                            <td class="flex gap-2">
                                <a href="{{ route('parts.edit', $part) }}" class="btn btn-sm btn-secondary"><i class="fa-solid fa-pen"></i></a>
                                <form action="{{ route('parts.destroy', $part) }}" method="POST" onsubmit="return confirm('Delete this part?')">
                                    @csrf @method('DELETE')
                                    <button class="btn btn-sm btn-danger"><i class="fa-solid fa-trash"></i></button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="6" class="text-center text-muted">No direct parts used.</td></tr>
                    @endforelse
                </tbody>
                <tfoot>
                    <tr style="background-color: #f8fafc; font-weight: bold;">
                        <td colspan="4" class="text-right">Total Direct Parts:</td>
                        <td>{{ number_format($directPartsTotal, 2) }}</td>
                        <td></td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>

    <div class="card">
        <h2 class="mb-4">Status History</h2>
        <div class="table-container">
            <table class="table">
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Status</th>
                        <th>Notes</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($equipment->statusHistory()->latest()->get() as $history)
                        <tr>
                            <td>{{ $history->created_at->format('Y-m-d H:i') }}</td>
                            <td>
                                <span class="badge badge-{{ $history->status->color }}">
                                    {{ $history->status->name }}
                                </span>
                            </td>
                            <td>{{ $history->notes }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
@endsection
