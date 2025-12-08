@extends('layouts.app')

@section('title', 'Work History')

@section('content')
<style>
.ms-table {
    border-collapse: collapse;
    width: 100%;
}

.ms-table th,
.ms-table td {
    border: 1px solid #ddd;
    padding: 4px;
    text-align: left;
}

.ms-table th {
    background-color: #f2f2f2;
}

.ms-table tr:nth-child(even) {
    background-color: #f9f9f9;
}
</style>

<div class="header">
    <h1 class="page-title" style="font-size: 1.2rem;">Պատմություն հին համարով: {{ $serial }}</h1>
    <a href="{{ url()->previous() }}" class="btn btn-m btn-secondary">
        <i class="fa-solid fa-arrow-left"></i> Նախորդ էջ
    </a>
</div>

<div class="data-table-wrapper">
    <table class="ms-table">
        <thead>
            <tr>
                <th>Մուտքի ամսաթիվ</th>
                <th>Ելքի ամսաթիվ</th>
                <th>Հին համար</th>
                <th>Նոր համար</th>
                <th>ԳԳՄ</th>
                <th>ՏՏ</th>
                <th>Սարք</th>
                <th>Կարգ</th>
                <th>Կարգի գումար</th>
                <th>Կարգավիխակ</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse($works as $work)
                <tr>
                    <td>{{ $work->receive_date ? $work->receive_date->format('Y-m-d') : '-' }}</td>
                    <td>{{ $work->exit_date ? $work->exit_date->format('Y-m-d') : '-' }}</td>
                    <td>{{ $work->old_serial_number ?? '-' }}</td>
                    <td>{{ $work->new_serial_number ?? '-' }}</td>
                    <td>{{ $work->partner->region ?? '-' }}</td>
                    <td>{{ $work->partnerStructure->name ?? '-' }}</td>
                    <td>{{ $work->equipment->name ?? '-' }}</td>
                    <td>{{ $work->equipmentPartGroup->name ?? '-' }}</td>
                    <td>{{ $work->equipment_part_group_total_price?? '-' }}</td>
                    <td>{{ $work->status ? 'արխիվ' : 'ընթացիկ' }}</td>
                    <td>
                        <a href="{{ route('works.edit', $work) }}" class="btn btn-sm btn-primary">Edit</a>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="10" style="text-align: center;">No works found for this serial number.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection
