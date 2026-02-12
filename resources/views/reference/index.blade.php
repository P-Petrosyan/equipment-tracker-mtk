@extends('layouts.app')

@section('title', 'Հաշվետվություններ')

@section('content')
<div class="container-fluid">
    <h2>Հաշվետվություններ</h2>

    <div class="data-table-wrapper" style="margin-bottom: 5px;">
        <form id="reference-form" method="GET" action="{{ route('reference.index') }}" style="display: flex; gap: 10px; align-items: end;">
            <div>
                <label>Սկսման ամսաթիվ:</label>
                <input type="date" name="start_date" id="start_date" class="form-control border p-2" style="max-width: 165px" value="{{ $startDate }}" required>
            </div>
            <div>
                <label>Ավարտի ամսաթիվ:</label>
                <input type="date" name="end_date" id="end_date" class="form-control border p-2" style="max-width: 165px" value="{{ $endDate }}" required>
            </div>
            <button type="submit" class="btn btn-sm btn-primary">Ցույց տալ</button>
            <div style="margin-left: auto;">
                <a href="{{ route('reference.trilateral', ['start_date' => $startDate, 'end_date' => $endDate]) }}" class="btn btn-sm btn-info" style="margin-right: 5px" target="_blank">ՍՉԱՄ PDF</a>
                <a href="{{ route('reference.trilateral.word', ['start_date' => $startDate, 'end_date' => $endDate]) }}" class="btn btn-sm btn-success" style="margin-right: 5px" target="_blank">ՍՉԱՄ Word</a>
                <a href="{{ route('reference.export-products-by-regions', ['start_date' => $startDate, 'end_date' => $endDate]) }}" class="btn btn-sm btn-secondary" target="_blank">Դետալների ծախս</a>
            </div>
        </form>
    </div>

    <div class="data-table-wrapper" id="partner-groups" style="display: none; margin-bottom: 5px;">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 10px;">
            <h5>Գործընկերներ ըստ մարզերի</h5>
            <button id="print-parts-btn" class="btn btn-sm btn-success" onclick="printPartsUsed()" style="display: none;">Տպել ապրանքների բացվածք</button>
        </div>
        <div id="partners-content" style="display: flex; gap: 5px; overflow: auto"></div>
    </div>

    @if($acts->count() > 0)
    <div class="data-table-wrapper">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem;">
            <h5>Ակտեր ({{ $acts->count() }})</h5>
            <a href="{{ route('reference.print', ['start_date' => $startDate, 'end_date' => $endDate]) }}" class="btn btn-sm btn-info" target="_blank">Տպել Տեղեկանք</a>
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

input[type="date"]::-webkit-datetime-edit {
    display: flex;
    flex-direction: row-reverse;
}

input[type="date"]::-webkit-datetime-edit-day-field {
    order: 1;
}

input[type="date"]::-webkit-datetime-edit-month-field {
    order: 2;
}

input[type="date"]::-webkit-datetime-edit-year-field {
    order: 3;
}
</style>

<script>
// Load partners on page load if dates exist
window.addEventListener('DOMContentLoaded', function() {
    const startDate = document.getElementById('start_date').value;
    const endDate = document.getElementById('end_date').value;

    if (startDate && endDate) {
        loadPartners(startDate, endDate);
    }
});

document.getElementById('reference-form').addEventListener('submit', function(e) {
    const startDate = document.getElementById('start_date').value;
    const endDate = document.getElementById('end_date').value;

    if (startDate && endDate) {
        loadPartners(startDate, endDate);
    }
});

function loadPartners(startDate, endDate) {
    fetch(`/reference/partners-by-period?start_date=${startDate}&end_date=${endDate}`)
        .then(response => response.json())
        .then(data => {
            displayPartnersByRegion(data.partners);
        })
        .catch(error => console.error('Error:', error));
}

function displayPartnersByRegion(partnersByRegion) {
    const partnersDisplay = document.getElementById('partner-groups');
    const partnersContent = document.getElementById('partners-content');

    if (Object.keys(partnersByRegion).length === 0) {
        partnersContent.innerHTML = '<p class="text-muted">Այս ժամանակահատվածում գործընկերներ չկան</p>';
        partnersDisplay.style.display = 'block';
        return;
    }

    let html = '';

    Object.keys(partnersByRegion).forEach(region => {
        const partners = partnersByRegion[region];

        html += `
            <div style="margin-bottom: 5px; border: 1px solid #ddd; padding: 10px; border-radius: 5px;">
                <h6 style="margin-bottom: 10px; color: #333; font-weight: bold;">${region}</h6>
                <div style="display: flex; flex-wrap: wrap; gap: 10px;">
        `;

        partners.forEach(partner => {
            html += `
                <label style="display: flex; align-items: center; gap: 5px; padding: 5px 10px; border: 1px solid #ccc; border-radius: 3px; background: #f9f9f9;">
                    <input type="checkbox" value="${partner.id}" name="selected_partners[]">
                    <span>${partner.region}</span>
                </label>
            `;
        });

        html += `
                </div>
            </div>
        `;
    });

    partnersContent.innerHTML = html;
    partnersDisplay.style.display = 'block';

    // Add event listeners to checkboxes
    document.querySelectorAll('input[name="selected_partners[]"]').forEach(checkbox => {
        checkbox.addEventListener('change', togglePrintButton);
    });
}

function togglePrintButton() {
    const checkedBoxes = document.querySelectorAll('input[name="selected_partners[]"]:checked');
    const printBtn = document.getElementById('print-parts-btn');
    printBtn.style.display = checkedBoxes.length > 0 ? 'block' : 'none';
}

function printPartsUsed() {
    const checkedBoxes = document.querySelectorAll('input[name="selected_partners[]"]:checked');
    const partnerIds = Array.from(checkedBoxes).map(cb => cb.value);
    const startDate = document.getElementById('start_date').value;
    const endDate = document.getElementById('end_date').value;

    if (partnerIds.length === 0) {
        alert('Խնդրում ենք ընտրել գործընկեր');
        return;
    }

    const params = new URLSearchParams({
        partner_ids: partnerIds.join(','),
        start_date: startDate,
        end_date: endDate
    });

    window.open(`/reference/export-parts-used?${params}`, '_blank');
}
</script>
@endsection
