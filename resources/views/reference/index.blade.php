@extends('layouts.app')

@section('title', 'Հաշվետվություններ')

@section('content')
<div class="container-fluid">
    <h2>Հաշվետվություններ</h2>

    <div class="data-table-wrapper" style="margin-bottom: 20px;">
        <form method="GET" action="{{ route('reference.index') }}" style="display: flex; gap: 15px; align-items: end;">
            <div>
                <label>Սկսման ամսաթիվ:</label>
                <input type="date" name="start_date" class="form-control border p-2" value="{{ $startDate }}" required>
            </div>
            <div>
                <label>Ավարտի ամսաթիվ:</label>
                <input type="date" name="end_date" class="form-control border p-2" value="{{ $endDate }}" required>
            </div>
            <button type="submit" class="btn btn-primary">Ցույց տալ</button>
        </form>
    </div>

    @if($acts->count() > 0)
    <div class="data-table-wrapper">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem;">
            <h5>Ակտեր ({{ $acts->count() }})</h5>
            <a href="{{ route('reference.print', ['start_date' => $startDate, 'end_date' => $endDate]) }}" class="btn btn-info" target="_blank">Տպել Տեղեկանք</a>
        </div>
        <table class="ms-table">
            <thead>
                <tr>
                    <th>Գործընկեր</th>
                    <th>Ամսաթիվ</th>
                    <th>Ակտի համար</th>
                </tr>
            </thead>
            <tbody>
                @foreach($acts as $act)
                    <tr>
                        <td>{{ $act->partner->region }}</td>
                        <td>{{ $act->act_date->format('d.m.Y') }}</td>
                        <td>{{ $act->act_number }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @elseif($startDate && $endDate)
    <div class="data-table-wrapper">
        <p class="text-center text-muted">Ընտրված ժամանակահատվածում ակտեր չկան</p>
    </div>
    @endif
</div>

<style>
.data-table-wrapper {
    background: white;
    border-radius: 8px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    padding: 1rem;
}

.ms-table {
    width: 100%;
    border-collapse: collapse;
    margin-top: 0.5rem;
}

.ms-table th,
.ms-table td {
    padding: 8px 12px;
    text-align: left;
    border-bottom: 1px solid #ddd;
}

.ms-table th {
    background-color: #f8f9fa;
    font-weight: 600;
}

.container-fluid {
    max-width: 1200px;
    margin: 0 auto;
}
</style>
@endsection
