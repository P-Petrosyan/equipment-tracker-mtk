@extends('layouts.app')

@section('title', 'Դետալների մուտք')

@section('content')
<style>
.ms-table {
    border-collapse: collapse;
    width: 100%;
}

.ms-table th,
.ms-table td {
    border: 1px solid #ddd;
    padding: 8px;
    text-align: left;
}

.ms-table th {
    background-color: #f2f2f2;
    font-weight: bold;
}

.ms-table tr:nth-child(even) {
    background-color: #f9f9f9;
}
</style>

<div class="header mb-2">
    <h1 class="page-title" style="font-size: 1.2rem;">Դետալների մուտք (Parts Entry)</h1>
    <a href="{{ route('dashboard') }}" class="btn btn-sm btn-secondary">
        <i class="fa-solid fa-arrow-left"></i> Գլխավոր էջ
    </a>
</div>
<div class="data-table-wrapper">
    <table class="ms-table">
        <thead>
            <tr>
                <th>Code</th>
                <th>Type</th>
                <th>Name</th>
                <th>Price</th>
                <th>Current Quantity</th>
                <th>Add Quantity</th>
                <th>Measure Unit</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @if(isset($parts))
                @foreach($parts as $part)
                    <tr id="part-row-{{ $part->id }}">
                        <td>{{ $part->code }}</td>
                        <td>{{ $part->type }}</td>
                        <td>{{ $part->name }}</td>
                        <td>{{ number_format($part->unit_price, 2) }}</td>
                        <td>{{ $part->quantity ?? 0 }}</td>
                        <td>
                            <input type="number" id="add-quantity-{{ $part->id }}" placeholder="0" class="border p-1 w-full" min="0">
                        </td>
                        <td>{{ $part->measure_unit }}</td>
                        <td>
                            <button onclick="addQuantity({{ $part->id }})" class="text-green-600 font-bold">Add</button>
                        </td>
                    </tr>
                @endforeach
            @endif
        </tbody>
    </table>
</div>

<script>
    function addQuantity(partId) {
        const input = document.getElementById(`add-quantity-${partId}`);
        const addQty = parseFloat(input.value);

        if (!addQty || addQty <= 0) {
            alert('Please enter a valid quantity to add');
            return;
        }

        const formData = new FormData();
        formData.append('_token', '{{ csrf_token() }}');
        formData.append('add_quantity', addQty);

        fetch(`/parts/${partId}/add-quantity`, {
            method: 'POST',
            body: formData,
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json'
            }
        })
        .then(response => {
            if (response.ok) {
                return response.json();
            }
            throw new Error('Network response was not ok');
        })
        .then(data => {
            // Update the current quantity display
            const row = document.getElementById(`part-row-${partId}`);
            const quantityCell = row.cells[5]; // Current Quantity column
            quantityCell.textContent = data.new_quantity;

            // Clear the input
            input.value = '';

            // Show success message
            alert(`Quantity added successfully. New total: ${data.new_quantity}`);
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Error adding quantity');
        });
    }
</script>
@endsection
