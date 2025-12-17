<div class="section-header">Parts Snapshots</div>
<div style="margin-bottom: 10px; display: flex; gap: 10px; align-items: center;">
    <select id="snapshot-selector" class="form-control" style="width: auto;">
        <option value="">Ընտրել ամսաթիվը</option>
        @if(isset($data['snapshots']))
            @foreach($data['snapshots'] as $snapshot)
                <option value="{{ $snapshot->snapshot_date }}">{{ $snapshot->snapshot_date->format('d-m-Y') }}</option>
            @endforeach
        @endif
    </select>
    <button onclick="viewSelectedSnapshot()" class="btn btn-m btn-primary">
        <i class="fa-solid fa-eye"></i> Ընտրել
    </button>
</div>

<div id="snapshot-table" style="display: none;">
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
            <tbody id="snapshot-tbody">
            </tbody>
        </table>
    </div>
</div>

<script>
function viewSelectedSnapshot() {
    const selector = document.getElementById('snapshot-selector');
    const selectedDate = selector.value;

    if (!selectedDate) {
        alert('Please select a snapshot date');
        return;
    }

    fetch(`{{ url('/parts/snapshot-data') }}/${selectedDate}`)
    .then(response => response.json())
    .then(data => {
        const tbody = document.getElementById('snapshot-tbody');
        tbody.innerHTML = '';

        data.parts_data.forEach(part => {
            const row = document.createElement('tr');
            row.innerHTML = `
                <td>${part.code}</td>
                <td>${part.name}</td>
                <td>${parseFloat(part.unit_price).toFixed(2)}</td>
                <td>${part.quantity || 0}</td>
                <td>${part.used_quantity || 0}</td>
                <td>${part.measure_unit}</td>
            `;
            tbody.appendChild(row);
        });

        document.getElementById('snapshot-table').style.display = 'block';
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Error loading snapshot data');
    });
}
</script>
