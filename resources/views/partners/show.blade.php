@extends('layouts.app')

@section('title', $partner->name)

@section('content')
    <div class="header">
        <h1 class="page-title">{{ $partner->name }}</h1>
        <div class="flex gap-2">
            <a href="{{ route('partners.edit', $partner) }}" class="btn btn-secondary">
                <i class="fa-solid fa-pen"></i> Edit
            </a>
            <a href="{{ route('partners.index') }}" class="btn btn-secondary">
                <i class="fa-solid fa-arrow-left"></i> Back
            </a>
        </div>
    </div>

    <div class="grid grid-cols-2 gap-4 mb-4">
        <div class="card">
            <h3 class="text-muted mb-4">Contact Info</h3>
            <p><strong>Contact Person:</strong> {{ $partner->contact_person ?? 'N/A' }}</p>
            <p><strong>Phone:</strong> {{ $partner->phone ?? 'N/A' }}</p>
            <p><strong>Email:</strong> {{ $partner->email ?? 'N/A' }}</p>
            <p><strong>Address:</strong> {{ $partner->address ?? 'N/A' }}</p>
        </div>
        <div class="card">
            <h3 class="text-muted mb-4">Notes</h3>
            <p>{{ $partner->notes ?? 'No notes available.' }}</p>
        </div>
    </div>

    <div class="card">
        <div class="flex justify-between items-center mb-4">
            <h2>Equipment History</h2>
            <a href="{{ route('equipment.create', ['partner_id' => $partner->id]) }}" class="btn btn-primary btn-sm">
                <i class="fa-solid fa-plus"></i> Add Equipment
            </a>
        </div>

        <div class="table-container">
            <table class="table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Model</th>
                        <th>Serial</th>
                        <th>Status</th>
                        <th>Received</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($partner->equipment as $item)
                        <tr>
                            <td>{{ $item->internal_id ?? $item->id }}</td>
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
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" style="text-align: center;">No equipment records found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection