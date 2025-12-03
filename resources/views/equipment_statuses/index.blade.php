@extends('layouts.app')

@section('title', 'Equipment Statuses')

@section('content')
    <div class="header">
        <h1 class="page-title">Equipment Statuses</h1>
        <a href="{{ route('equipment-statuses.create') }}" class="btn btn-primary">
            <i class="fa-solid fa-plus"></i> Add Status
        </a>
    </div>

    @if(session('error'))
        <div class="card"
            style="background-color: #fee2e2; color: #991b1b; border: 1px solid #fecaca; padding: 1rem; margin-bottom: 1rem;">
            <i class="fa-solid fa-circle-exclamation"></i> {{ session('error') }}
        </div>
    @endif

    <div class="card">
        <div class="table-container">
            <table class="table">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Color</th>
                        <th>Preview</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($statuses as $status)
                        <tr>
                            <td>{{ $status->name }}</td>
                            <td>{{ ucfirst($status->color) }}</td>
                            <td>
                                <span class="badge badge-{{ $status->color }}">
                                    {{ $status->name }}
                                </span>
                            </td>
                            <td class="flex gap-2">
                                <a href="{{ route('equipment-statuses.edit', $status) }}" class="btn btn-sm btn-secondary">
                                    <i class="fa-solid fa-pen"></i>
                                </a>
                                <form action="{{ route('equipment-statuses.destroy', $status) }}" method="POST"
                                    onsubmit="return confirm('Are you sure? This action cannot be undone.')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-danger">
                                        <i class="fa-solid fa-trash"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" style="text-align: center; color: var(--text-muted);">No statuses found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
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