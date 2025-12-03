@extends('layouts.app')

@section('title', 'Կատարողական ակտեր')

@section('content')
    <div class="header">
        <h1 class="page-title">Կատարողական ակտեր</h1>
        <a href="{{ route('dashboard') }}" class="btn btn-secondary">
            <i class="fa-solid fa-arrow-left"></i> Հետ
        </a>
    </div>

    <div class="card">
        <table class="table">
            <thead>
                <tr>
                    <th>Ամսաթիվ</th>
                    <th>Սարքավորում</th>
                    <th>Գործընկեր</th>
                    <th>Տեխնիկ</th>
                    <th>Նկարագրություն</th>
                    <th>Գործողություններ</th>
                </tr>
            </thead>
            <tbody>
                @forelse($workOrders as $workOrder)
                    <tr>
                        <td>{{ $workOrder->created_at->format('Y-m-d') }}</td>
                        <td>{{ $workOrder->equipment->model }} ({{ $workOrder->equipment->serial_number }})</td>
                        <td>{{ $workOrder->equipment->partner->name }}</td>
                        <td>{{ $workOrder->technician_name }}</td>
                        <td>{{ Str::limit($workOrder->description, 50) }}</td>
                        <td>
                            <a href="{{ route('equipment.show', $workOrder->equipment) }}"
                                class="btn btn-sm btn-secondary">Դիտել</a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="text-center">Կատարողական ակտեր չեն գտնվել:</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
        <div class="mt-4">
            {{ $workOrders->links() }}
        </div>
    </div>
@endsection