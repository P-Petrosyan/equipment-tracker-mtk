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
                    <tr id="act-row-{{ $act->id }}" onclick="selectAct({{ $act->id }}, {{ $act->partner_id }}, '{{ $act->act_date->format('Y-m-d') }}', this)" style="cursor: pointer;">
                        <td class="partner-cell" data-partner-id="{{ $act->partner_id }}">{{ $act->partner->region }}</td>
                        <td class="date-cell" data-date="{{ $act->act_date->format('Y-m-d') }}">{{ $act->act_date->format('d/m/Y') }}</td>
                        <td class="number-cell">{{ $act->act_number }}</td>
                        <td>
                            <button onclick="editAct({{ $act->id }}, event)" class="btn btn-sm btn-primary edit-btn">Edit</button>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
        </div>

        <!-- Archived Works Table -->
        <div class="data-table-wrapper" style="flex: 1;">
            <div style="display: flex; justify-content: space-between; align-items: center;">
                <h5>Արխիվացված աշխատանքներ</h5>
                <button onclick="addAllWorks()" class="btn btn-sm btn-success" id="add-all-btn" style="display: none;">Add All</button>
            </div>
        <table class="ms-table">
            <thead>
                <tr>
                    <th>Սարք</th>
{{--                    <th>--}}
{{--                        <input type="text" id="new-serial-search" placeholder="Նոր համար" style="padding: 5px; width: 100%;">--}}
{{--                    </th>--}}
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
        <div style="display: flex; justify-content: space-between; align-items: center; margin: 0 10px;">
            <div style="display: flex; align-items: center; gap: 10px; ">
                <h5>Ակտին նշանակված աշխատանքներ</h5>
                <button class="btn btn-sm btn-info" onclick="printAct()" id="print-act-btn" style="display: none;">Տպել ակտը</button>
                <button onclick="updateExitDates()" class="btn btn-sm btn-warning">Թարմացնել ելքի ամսաթվերը</button>
                <button onclick="generateHandoverPdf()" class="btn btn-sm btn-secondary" id="handover-pdf-btn" style="display: none;">Հանձնման ընդունման ակտ</button>
            </div>
            <button onclick="removeAllWorks()" class="btn btn-sm btn-danger" id="remove-all-btn" style="display: none;">Remove All</button>
        </div>
        <table class="ms-table">
            <thead>
                <tr>
                    <th>Սարք</th>
                    <th>
                        <input type="text" id="assigned-new-serial-search" placeholder="Նոր համար" style="padding: 5px; width: 100%;">
                    </th>
                    <th>Կարգի գումար</th>
                    <th>Կարգ</th>
                    <th>Չվերանորոգվող</th>
                    <th>Ստացման ամսաթիվ</th>
                    <th>Ելքի ամսաթիվ</th>
                    <th></th>
                </tr>
            </thead>
            <tbody id="assigned-works-body">
                <tr>
                    <td colspan="8" class="text-center text-muted">Ընտրեք ակտ՝ նշանակված աշխատանքները դիտելու համար</td>
                </tr>
            </tbody>
        </table>
        <div id="assigned-pagination" style="display: none; margin-top: 10px; text-align: center;">
            <!-- Pagination will be inserted here -->
        </div>
    </div>
</div>

<script>
let selectedActId = null;
let currentPage = 1;
let selectedActData = null;
let assignedSearchTerm = '';

// Add search functionality for new serial number
document.addEventListener('DOMContentLoaded', function() {
    // const searchInput = document.getElementById('new-serial-search');
    // if (searchInput) {
    //     searchInput.addEventListener('input', function() {
    //         filterArchivedWorks(this.value);
    //     });
    // }

    const assignedSearchInput = document.getElementById('assigned-new-serial-search');
    if (assignedSearchInput) {
        assignedSearchInput.addEventListener('input', function() {
            assignedSearchTerm = this.value;
            if (selectedActData) {
                loadActWorks(selectedActData.actId, selectedActData.partnerId, selectedActData.actDate, 1, assignedSearchTerm);
            }
        });
    }
});

function filterArchivedWorks(searchTerm) {
    const tbody = document.getElementById('archived-works-body');
    const rows = tbody.getElementsByTagName('tr');

    for (let row of rows) {
        const cells = row.getElementsByTagName('td');
        if (cells.length > 1) {
            const newSerialCell = cells[1]; // Second column is "Նոր համար"
            const newSerial = newSerialCell.textContent || newSerialCell.innerText;

            if (newSerial.toLowerCase().includes(searchTerm.toLowerCase())) {
                row.style.display = '';
            } else {
                row.style.display = 'none';
            }
        }
    }
}

function selectAct(actId, partnerId, actDate, row) {
    selectedActId = actId;
    currentPage = 1;
    assignedSearchTerm = '';
    selectedActData = { actId, partnerId, actDate, row };

    // Clear search inputs
    // const searchInput = document.getElementById('new-serial-search');
    // if (searchInput) {
    //     searchInput.value = '';
    // }

    const assignedSearchInput = document.getElementById('assigned-new-serial-search');
    if (assignedSearchInput) {
        assignedSearchInput.value = '';
    }

    // Show print button and handover button
    document.getElementById('print-act-btn').style.display = 'inline-block';
    document.getElementById('handover-pdf-btn').style.display = 'inline-block';

    // Highlight selected row
    document.querySelectorAll('#acts-table tr').forEach(r => r.classList.remove('selected'));
    row.classList.add('selected');

    loadActWorks(actId, partnerId, actDate, 1, '');
}

function loadActWorks(actId, partnerId, actDate, page, search = '') {
    // Fetch archived works and assigned works
    fetch(`/acts/archived-works?partner_id=${partnerId}&act_date=${actDate}&act_id=${actId}&page=${page}&search=${encodeURIComponent(search)}`)
        .then(response => response.json())
        .then(data => {
            // Update archived works table
            const archivedTbody = document.getElementById('archived-works-body');
            archivedTbody.innerHTML = '';

            if (data.archived_works.length > 0) {
                document.getElementById('add-all-btn').style.display = 'block';
                data.archived_works.forEach(work => {
                    const tr = document.createElement('tr');
                    tr.innerHTML = `
                        <td>${work.equipment.name}</td>
                        <td>${work.new_serial_number || '-'}</td>
                        <td>${work.equipment_part_group_total_price || '-'}</td>
                        <td>${work.equipment_part_group ? work.equipment_part_group.name : '-'}</td>
                        <td>${work.receive_date ? new Date(work.receive_date).toLocaleDateString('en-GB') : ''}</td>
                        <td>${work.exit_date ? new Date(work.exit_date).toLocaleDateString('en-GB') : ''}</td>
                        <td><button onclick="assignWork(${work.id})" class="btn btn-sm btn-primary hover:underline">Add</button></td>
                    `;
                    archivedTbody.appendChild(tr);
                });
            } else {
                document.getElementById('add-all-btn').style.display = 'none';
                archivedTbody.innerHTML = '<tr><td colspan="7" class="text-center text-muted">Արխիվացված աշխատանքներ չկան</td></tr>';
            }

            // Update assigned works table
            const assignedTbody = document.getElementById('assigned-works-body');
            assignedTbody.innerHTML = '';

            if (data.assigned_works.length > 0) {
                document.getElementById('remove-all-btn').style.display = 'block';
                data.assigned_works.forEach(work => {
                    const tr = document.createElement('tr');
                    tr.innerHTML = `
                        <td>${work.equipment.name}</td>
                        <td>${work.new_serial_number || '-'}</td>
                        <td>${work.equipment_part_group_total_price || '-'}</td>
                        <td>${work.equipment_part_group ? work.equipment_part_group.name : '-'}</td>
                        <td>${work.non_repairable ? 'Այո' : 'Ոչ'}</td>
                        <td>${work.receive_date ? new Date(work.receive_date).toLocaleDateString('en-GB') : ''}</td>
                        <td>${work.exit_date ? new Date(work.exit_date).toLocaleDateString('en-GB') : ''}</td>
                        <td><button onclick="removeWork(${work.id})" class="btn btn-sm btn-danger hover:underline">Remove</button></td>
                    `;
                    assignedTbody.appendChild(tr);
                });

                // Update pagination
                if (data.pagination) {
                    updatePagination(data.pagination);
                }
            } else {
                document.getElementById('remove-all-btn').style.display = 'none';
                document.getElementById('assigned-pagination').style.display = 'none';
                assignedTbody.innerHTML = '<tr><td colspan="8" class="text-center text-muted">Նշանակված աշխատանքներ չկան</td></tr>';
            }
        })
        .catch(error => {
            console.error('Error:', error);
        });
}

function updatePagination(pagination) {
    const paginationDiv = document.getElementById('assigned-pagination');

    if (pagination.last_page <= 1) {
        paginationDiv.style.display = 'none';
        return;
    }

    paginationDiv.style.display = 'block';
    let html = '<div style="display: flex; gap: 5px; justify-content: center; align-items: center;">';

    // Previous button
    if (pagination.current_page > 1) {
        html += `<button onclick="changePage(${pagination.current_page - 1})" class="btn btn-sm btn-secondary">Նախորդ</button>`;
    }

    // Page numbers
    for (let i = 1; i <= pagination.last_page; i++) {
        if (i === pagination.current_page) {
            html += `<button class="btn btn-sm btn-primary" disabled>${i}</button>`;
        } else {
            html += `<button onclick="changePage(${i})" class="btn btn-sm btn-secondary">${i}</button>`;
        }
    }

    // Next button
    if (pagination.current_page < pagination.last_page) {
        html += `<button onclick="changePage(${pagination.current_page + 1})" class="btn btn-sm btn-secondary">Հաջորդ</button>`;
    }
    html += `<span class="btn btn-sm">${pagination.total}</span>`

    html += '</div>';
    paginationDiv.innerHTML = html;
}

function changePage(page) {
    currentPage = page;
    if (selectedActData) {
        loadActWorks(selectedActData.actId, selectedActData.partnerId, selectedActData.actDate, page, assignedSearchTerm);
    }
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
        if (selectedActData) {
            loadActWorks(selectedActData.actId, selectedActData.partnerId, selectedActData.actDate, currentPage, assignedSearchTerm);
        }
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
        if (selectedActData) {
            loadActWorks(selectedActData.actId, selectedActData.partnerId, selectedActData.actDate, currentPage, assignedSearchTerm);
        }
    });
}

function addAllWorks() {
    fetch('/acts/add-all-works', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify({
            act_id: selectedActId
        })
    })
    .then(response => response.json())
    .then(data => {
        if (selectedActData) {
            loadActWorks(selectedActData.actId, selectedActData.partnerId, selectedActData.actDate, 1);
        }
    });
}

function removeAllWorks() {
    fetch('/acts/remove-all-works', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify({
            act_id: selectedActId
        })
    })
    .then(response => response.json())
    .then(data => {
        if (selectedActData) {
            loadActWorks(selectedActData.actId, selectedActData.partnerId, selectedActData.actDate, 1);
        }
    });
}

function printAct() {
    if (!selectedActId) {
        alert('Խնդրում ենք ընտրել ակտ');
        return;
    }

    window.open(`/acts/${selectedActId}/print`, '_blank');
}

function updateExitDates() {
    if (!selectedActId) {
        alert('Խնդրում ենք ընտրել ակտ');
        return;
    }

    fetch('/acts/update-exit-dates', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify({
            act_id: selectedActId
        })
    })
    .then(response => response.json())
    .then(data => {
        if (selectedActData) {
            loadActWorks(selectedActData.actId, selectedActData.partnerId, selectedActData.actDate, currentPage, assignedSearchTerm);
        }
    });
}

function generateHandoverPdf() {
    if (!selectedActId) {
        alert('Խնդրում ենք ընտրել ակտ');
        return;
    }

    window.open(`/acts/${selectedActId}/handover-pdf`, '_blank');
}

const partners = @json($partners);

function editAct(actId, event) {
    event.stopPropagation();
    const row = document.getElementById(`act-row-${actId}`);
    const partnerCell = row.querySelector('.partner-cell');
    const dateCell = row.querySelector('.date-cell');
    const numberCell = row.querySelector('.number-cell');
    const actionCell = row.querySelector('td:last-child');

    const partnerId = partnerCell.dataset.partnerId;
    const currentDate = dateCell.dataset.date;
    const currentNumber = numberCell.textContent;

    let partnerOptions = '';
    partners.forEach(partner => {
        const selected = partnerId == partner.id ? 'selected' : '';
        partnerOptions += `<option value="${partner.id}" ${selected}>${partner.region}</option>`;
    });

    partnerCell.innerHTML = `<select class="border p-1 w-full edit-partner">${partnerOptions}</select>`;
    dateCell.innerHTML = `<input type="date" value="${currentDate}" class="border p-1 w-full edit-date">`;
    numberCell.innerHTML = `<input type="text" value="${currentNumber}" class="border p-1 w-full edit-number">`;

    actionCell.innerHTML = `
        <button onclick="saveAct(${actId})" class="btn btn-sm btn-primary">Save</button>
        <button onclick="cancelEdit(${actId})" class="btn btn-sm btn-secondary">Cancel</button>
    `;

    row.onclick = null;
}

function saveAct(actId) {
    const row = document.getElementById(`act-row-${actId}`);
    const partnerId = row.querySelector('.edit-partner').value;
    const actDate = row.querySelector('.edit-date').value;
    const actNumber = row.querySelector('.edit-number').value;

    fetch(`/acts/${actId}`, {
        method: 'PUT',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify({
            partner_id: partnerId,
            act_date: actDate,
            act_number: actNumber
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            location.reload();
        } else {
            alert('Error updating act');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Error updating act');
    });
}

function cancelEdit(actId) {
    location.reload();
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
