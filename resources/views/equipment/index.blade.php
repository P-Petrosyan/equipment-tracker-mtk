@extends('layouts.app')

@section('title', 'Equipment')

@section('content')
    <div class="header">
        <h1 class="page-title">Equipment</h1>
        <a href="{{ route('equipment.create') }}" class="btn btn-primary">
            <i class="fa-solid fa-plus"></i> Add Equipment
        </a>
    </div>

    <div class="card">
        <form action="{{ route('equipment.index') }}" method="GET" class="flex gap-4 mb-4 items-end">
            <div class="form-group mb-0" style="flex: 1;">
                <label class="form-label">Search</label>
                <input type="text" name="search" class="form-control" placeholder="Search serial, model..."
                    value="{{ request('search') }}">
            </div>
            <div class="form-group mb-0" style="width: 200px;">
                <label class="form-label">Status</label>
                <select name="status_id" class="form-control">
                    <option value="">All Statuses</option>
                    @foreach($statuses as $status)
                        <option value="{{ $status->id }}" {{ request('status_id') == $status->id ? 'selected' : '' }}>
                            {{ $status->name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="form-group mb-0" style="width: 200px;">
                <label class="form-label">Partner</label>
                <select name="partner_id" class="form-control">
                    <option value="">All Partners</option>
                    @foreach($partners as $partner)
                        <option value="{{ $partner->id }}" {{ request('partner_id') == $partner->id ? 'selected' : '' }}>
                            {{ $partner->name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <button type="submit" class="btn btn-secondary" style="height: 42px;">Filter</button>
        </form>

        <div class="table-container">
            <table class="table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Partner</th>
                        <th>Model</th>
                        <th>Serial</th>
                        <th>Status</th>
                        <th>Received</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($equipment as $item)
                        <tr>
                            <td>{{ $item->internal_id ?? $item->id }}</td>
                            <td>
                                <a href="{{ route('partners.show', $item->partner) }}"
                                    style="color: var(--primary-color); text-decoration: none;">
                                    {{ $item->partner->name }}
                                </a>
                            </td>
                            <td>{{ $item->model }}</td>
                            <td>{{ $item->serial_number }}</td>
                            <td>
                                <span class="badge badge-{{ $item->status->color }}">
                                    {{ $item->status->name }}
                                </span>
                            </td>
                            <td>{{ $item->received_at ? $item->received_at->format('Y-m-d') : '-' }}</td>
                            <td>
                                <a href="{{ route('equipment.show', $item) }}" class="btn btn-sm btn-secondary">
                                    <i class="fa-solid fa-eye"></i>
                                </a>
                                <a href="{{ route('equipment.edit', $item) }}" class="btn btn-sm btn-secondary">
                                    <i class="fa-solid fa-pen"></i>
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" style="text-align: center; color: var(--text-muted);">No equipment found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-4">
            {{ $equipment->links() }}
        </div>
    </div>

    <script>
        // Clear all form drafts when visiting the dashboard
        const draftKeys = [
            'partner_create_draft', 'partner_edit_draft',
            'equipment_create_draft', 'equipment_edit_draft',
            'work_order_create_draft', 'work_order_edit_draft',
            'part_create_draft', 'part_edit_draft',
            'status_create_draft', 'status_edit_draft',
            'report_generate_draft'
        ];

        draftKeys.forEach(key => localStorage.removeItem(key));
    </script>
@endsection