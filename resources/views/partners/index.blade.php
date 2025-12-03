@extends('layouts.app')

@section('title', 'Partners')

@section('content')
    <div class="header">
        <h1 class="page-title">Partners</h1>
        <a href="{{ route('partners.create') }}" class="btn btn-primary">
            <i class="fa-solid fa-plus"></i> Add Partner
        </a>
    </div>

    <div class="card">
        <form action="{{ route('partners.index') }}" method="GET" class="flex gap-4 mb-4">
            <input type="text" name="search" class="form-control" placeholder="Search partners..."
                value="{{ request('search') }}">
            <button type="submit" class="btn btn-secondary">Search</button>
        </form>

        <div class="table-container">
            <table class="table">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Contact Person</th>
                        <th>Phone</th>
                        <th>Email</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($partners as $partner)
                        <tr>
                            <td>{{ $partner->name }}</td>
                            <td>{{ $partner->contact_person ?? '-' }}</td>
                            <td>{{ $partner->phone ?? '-' }}</td>
                            <td>{{ $partner->email ?? '-' }}</td>
                            <td class="flex gap-2">
                                <a href="{{ route('partners.show', $partner) }}" class="btn btn-sm btn-secondary">
                                    <i class="fa-solid fa-eye"></i>
                                </a>
                                <a href="{{ route('partners.edit', $partner) }}" class="btn btn-sm btn-secondary">
                                    <i class="fa-solid fa-pen"></i>
                                </a>
                                <form action="{{ route('partners.destroy', $partner) }}" method="POST"
                                    onsubmit="return confirm('Are you sure?')">
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
                            <td colspan="5" style="text-align: center; color: var(--text-muted);">No partners found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-4">
            {{ $partners->links() }}
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