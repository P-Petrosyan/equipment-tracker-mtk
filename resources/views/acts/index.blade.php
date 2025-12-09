@extends('layouts.app')

@section('title', 'Կատարողական ակտեր')

@section('content')
<div class="container-fluid">
    <h2>Կատարողական ակտեր</h2>

    <div style="display: flex; flex-direction: row; gap: 20px;">
        <!-- Acts Table -->
        <div class="data-table-wrapper" style="flex: 1; height: 400px; overflow-y: auto;">
            <h5>Ակտեր</h5>
        <table class="ms-table" id="acts-table">
            <thead>
                <tr>
                    <th>Գործընկեր</th>
                    <th>Ամսաթիվ</th>
                    <th>Ակտի համար</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
            <tr>
                <form action="{{ route('acts.store') }}" method="POST">
                    @csrf
                    <td>
                        <select name="partner_id" class="border p-1 w-full" required>
                            <option value="">Ընտրել գործընկեր</option>
                            @foreach($partners as $partner)
                                <option value="{{ $partner->id }}">{{ $partner->region }}</option>
                            @endforeach
                        </select>
                    </td>
                    <td>
                        <input type="date" name="act_date" class="border p-1 w-full" required>
                    </td>
                    <td>
                        <input type="text" name="act_number" placeholder="Ակտի համար" class="border p-1 w-full" required>
                    </td>
                    <td>
                        <button type="submit" class="btn btn-sm btn-success font-bold">Add</button>
                    </td>
                </form>
            </tr>
                @foreach($acts as $act)
                    <tr onclick="selectAct({{ $act->id }}, {{ $act->partner_id }}, '{{ $act->act_date->format('Y-m-d') }}', this)" style="cursor: pointer;">
                        <td>{{ $act->partner->region }}</td>
                        <td>{{ $act->act_date->format('d/m/Y') }}</td>
                        <td>{{ $act->act_number }}</td>
                        <td>
                            <form action="{{ route('acts.destroy', $act) }}" method="POST" onsubmit="return confirm('Are you sure?');" style="display:inline;">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-danger hover:underline">Delete</button>
                            </form>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
        </div>

        <!-- Archived Works Table -->
        <div class="data-table-wrapper" style="flex: 1;">
            <h5>Արխիվացված աշխատանքներ</h5>
        <table class="ms-table">
            <thead>
                <tr>
                    <th>Սարք</th>
                    <th>Նոր համար</th>
                    <th>Կարգի գումար</th>
                    <th>Կարգ</th>
                    <th>Ստացման ամսաթիվ</th>
                    <th>Ելքի ամսաթիվ</th>
                    <th></th>
                </tr>
            </thead>
            <tbody id="archived-works-body">
                <tr>
                    <td colspan="7" class="text-center text-muted">Ընտրեք ակտ՝ արխիվացված աշխատանքները դիտելու համար</td>
                </tr>
            </tbody>
        </table>
        </div>
    </div>

    <!-- Assigned Works Table -->
    <div class="data-table-wrapper" style="margin-top: 10px;">
        <h5>Ակտին նշանակված աշխատանքներ</h5>
        <table class="ms-table">
            <thead>
                <tr>
                    <th>Սարք</th>
                    <th>Նոր համար</th>
                    <th>Կարգի գումար</th>
                    <th>Կարգ</th>
                    <th>Ստացման ամսաթիվ</th>
                    <th>Ելքի ամսաթիվ</th>
                    <th></th>
                </tr>
            </thead>
            <tbody id="assigned-works-body">
                <tr>
                    <td colspan="7" class="text-center text-muted">Ընտրեք ակտ՝ նշանակված աշխատանքները դիտելու համար</td>
                </tr>
            </tbody>
        </table>
    </div>
</div>

<script>
let selectedActId = null;

function selectAct(actId, partnerId, actDate, row) {
    selectedActId = actId;

    // Highlight selected row
    document.querySelectorAll('#acts-table tr').forEach(r => r.classList.remove('selected'));
    row.classList.add('selected');

    // Fetch archived works and assigned works
    fetch(`/acts/archived-works?partner_id=${partnerId}&act_date=${actDate}&act_id=${actId}`)
        .then(response => response.json())
        .then(data => {
            // Update archived works table
            const archivedTbody = document.getElementById('archived-works-body');
            archivedTbody.innerHTML = '';

            if (data.archived_works.length > 0) {
                data.archived_works.forEach(work => {
                    const tr = document.createElement('tr');
                    tr.innerHTML = `
                        <td>${work.equipment.name}</td>
                        <td>${work.new_serial_number || '-'}</td>
                        <td>${work.equipment_part_group_total_price || '-'}</td>
                        <td>${work.equipment_part_group ? work.equipment_part_group.name : '-'}</td>
                        <td>${new Date(work.receive_date).toLocaleDateString('en-GB')}</td>
                        <td>${new Date(work.exit_date).toLocaleDateString('en-GB')}</td>
                        <td><button onclick="assignWork(${work.id})" class="btn btn-sm btn-primary hover:underline">Add</button></td>
                    `;
                    archivedTbody.appendChild(tr);
                });
            } else {
                archivedTbody.innerHTML = '<tr><td colspan="7" class="text-center text-muted">Արխիվացված աշխատանքներ չկան</td></tr>';
            }

            // Update assigned works table
            const assignedTbody = document.getElementById('assigned-works-body');
            assignedTbody.innerHTML = '';

            if (data.assigned_works.length > 0) {
                data.assigned_works.forEach(work => {
                    const tr = document.createElement('tr');
                    tr.innerHTML = `
                        <td>${work.equipment.name}</td>
                        <td>${work.new_serial_number || '-'}</td>
                        <td>${work.equipment_part_group_total_price || '-'}</td>
                        <td>${work.equipment_part_group ? work.equipment_part_group.name : '-'}</td>
                        <td>${new Date(work.receive_date).toLocaleDateString('en-GB')}</td>
                        <td>${new Date(work.exit_date).toLocaleDateString('en-GB')}</td>
                        <td><button onclick="removeWork(${work.id})" class="btn btn-sm btn-danger hover:underline">Remove</button></td>
                    `;
                    assignedTbody.appendChild(tr);
                });
            } else {
                assignedTbody.innerHTML = '<tr><td colspan="7" class="text-center text-muted">Նշանակված աշխատանքներ չկան</td></tr>';
            }
        })
        .catch(error => {
            console.error('Error:', error);
        });
}

function assignWork(workId) {
    fetch('/acts/assign-work', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify({
            act_id: selectedActId,
            work_id: workId
        })
    })
    .then(response => response.json())
    .then(data => {
        // Refresh the tables
        document.querySelector('#acts-table .selected').click();
    });
}

function removeWork(workId) {
    fetch('/acts/remove-work', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify({
            act_id: selectedActId,
            work_id: workId
        })
    })
    .then(response => response.json())
    .then(data => {
        // Refresh the tables
        document.querySelector('#acts-table .selected').click();
    });
}
</script>

<style>
.selected {
    background-color: #e3f2fd !important;
}

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
    padding: 4px 8px;
    text-align: left;
    border-bottom: 1px solid #ddd;
}

.ms-table th {
    background-color: #f8f9fa;
    font-weight: 600;
}

.ms-table tr:hover {
    background-color: #f5f5f5;
}

.container-fluid {
    max-width: 1200px;
    margin: 0 auto;
}
</style>
@endsection
