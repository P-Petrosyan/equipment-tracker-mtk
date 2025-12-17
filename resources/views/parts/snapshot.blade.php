@extends('layouts.app')

@section('title', 'Parts Snapshot - ' . $date)

@section('content')
<div class="header">
    <h1 class="page-title" style="font-size: 1.2rem;">Parts Snapshot - {{ $date }}</h1>
    <a href="{{ route('general-data', 'parts') }}" class="btn btn-sm btn-secondary">
        <i class="fa-solid fa-arrow-left"></i> Back to Parts
    </a>
</div>

<div class="data-table-wrapper">
    <table class="ms-table">
        <thead>
            <tr>
                <th>Կոդ</th>
                <th>Անվանում</th>
                <th>Գին</th>
                <th>Մնացորդ</th>
                <th>Ծախսած</th>
                <th>Չ/Մ</th>
            </tr>
        </thead>
        <tbody>
            @foreach($parts as $part)
                <tr>
                    <td>{{ $part['code'] }}</td>
                    <td>{{ $part['name'] }}</td>
                    <td>{{ number_format($part['unit_price'], 2) }}</td>
                    <td>{{ $part['quantity'] ?? 0 }}</td>
                    <td>{{ $part['used_quantity'] ?? 0 }}</td>
                    <td>{{ $part['measure_unit'] }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection