<div class="section-header">Parts Snapshots</div>
<div style="margin-bottom: 10px; display: flex; gap: 10px; align-items: center;">
    <select id="snapshot-selector" class="form-control" style="width: auto;">
        <option value="">Ընտրել ամսաթիվը</option>
        @if(isset($data['snapshots']))
            @foreach($data['snapshots'] as $snapshot)
                <option value="{{ $snapshot->snapshot_date }}">{{ $snapshot->snapshot_date->format('d-m-Y') }} {{ $snapshot->snapshot_comment ?? '' }}</option>
            @endforeach
        @endif
    </select>
    <button onclick="viewSelectedSnapshot()" class="btn btn-m btn-primary">
        <i class="fa-solid fa-eye"></i> Ընտրել
    </button>
    <button onclick="exportSelectedSnapshot()" class="btn btn-m btn-success" id="export-btn" style="display: none;">
        <i class="fa-solid fa-file-excel"></i> Export to Excel
    </button>
</div>

<div id="snapshot-table" style="display: none;">
    <div class="data-table-wrapper">
        <p id="snapshot_comment" class="mt-1 mb-1"></p>
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
        console.log(data.snapshot_comment);
        const comment = document.getElementById('snapshot_comment');
        comment.innerHTML = data.snapshot_comment;
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
        document.getElementById('export-btn').style.display = 'inline-block';
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Error loading snapshot data');
    });
}

function exportSelectedSnapshot() {
    const selector = document.getElementById('snapshot-selector');
    const selectedDate = selector.value;

    if (!selectedDate) {
        alert('Please select a snapshot date');
        return;
    }

    window.location.href = `{{ url('/parts/snapshot-export') }}/${selectedDate}`;
}
</script>
