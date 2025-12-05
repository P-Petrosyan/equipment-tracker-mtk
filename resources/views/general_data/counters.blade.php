<div class="section-header">Սարքեր/Կարգեր (Counters/Orders)</div>

<!-- Equipment Table -->
<div style="display: flex; flex-direction: row; gap: 10px;">

    <div class="data-table-wrapper" style="margin-bottom: 20px;">
        <h5>Equipment</h5>
        <table class="ms-table" id="equipment-table">
            <thead>
            <tr>
                <th>Name</th>
                <th>Stug Price</th>
                <th>Actions</th>
            </tr>
            </thead>
            <tbody>
            @if(isset($data['equipment']))
                @foreach($data['equipment'] as $equipment)
                    <tr id="equipment-row-{{ $equipment->id }}" onclick="selectEquipment({{ $equipment->id }}, this)" data-id="{{ $equipment->id }}" style="cursor: pointer;">
                        <td class="editable" data-field="name">{{ $equipment->name }}</td>
                        <td class="editable" data-field="stug_price">{{ $equipment->stug_price }}</td>
                        <td>
                            <button onclick="editEquipment({{ $equipment->id }}, event)" class="text-blue-600 hover:underline edit-btn">Edit</button>
                            <button onclick="saveEquipment({{ $equipment->id }}, event)" class="text-green-600 hover:underline save-btn" style="display:none;">Save</button>
                            <button onclick="cancelEditEquipment({{ $equipment->id }}, event)" class="text-gray-600 hover:underline cancel-btn" style="display:none;">Cancel</button>
                            <form action="{{ route('equipment.destroy', $equipment) }}" method="POST" onsubmit="return confirm('Are you sure?');" style="display:inline;">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-red-600 hover:underline">Delete</button>
                            </form>
                        </td>
                    </tr>
                @endforeach
            @else
                <tr>
                    <td colspan="3" class="text-center text-muted">No equipment available</td>
                </tr>
            @endif
            <tr>
                <form action="{{ route('equipment.store') }}" method="POST" onsubmit="return handleEquipmentSubmit(event)">
                    @csrf
                    <input type="hidden" name="redirect_to" value="counters">
                    <td>
                        <input type="text" name="name" placeholder="name" class="border p-1 w-full" required>
                    </td>
                    <td>
                        <input type="number" step="0.01" name="stug_price" placeholder="stug price" class="border p-1 w-full">
                    </td>
                    <td>
                        <button type="submit" class="text-green-600 font-bold">Add</button>
                    </td>
                </form>
            </tr>
            </tbody>
        </table>
    </div>
    <!-- Part Groups Table -->
    <div class="data-table-wrapper" style="margin-bottom: 20px;">
        <h5>Part Groups</h5>
        <table class="ms-table" id="groups-table">
            <thead>
            <tr>
                <th>Name</th>
                <th>Total Price</th>
                <th>Notes</th>
                <th>Actions</th>
            </tr>
            </thead>
            <tbody id="groups-body">
            <tr>
                <td colspan="4" class="text-center text-muted">Select equipment to view groups</td>
            </tr>
            </tbody>
        </table>
    </div>
</div>

<!-- Parts Table -->
<div class="data-table-wrapper">
    <h5>Parts</h5>
    <table class="ms-table">
        <thead>
        <tr>
            <th>Part Selection</th>
            <th>Name</th>
            <th>Type</th>
            <th>Quantity</th>
            <th>Unit Price</th>
            <th>Measure Unit</th>
            <th>Actions</th>
        </tr>
        </thead>
        <tbody id="parts-body">
        <tr>
            <td colspan="7" class="text-center text-muted">Select group to view parts</td>
        </tr>
        </tbody>
    </table>
</div>

<script>
    @if(isset($data['equipment']))
    const equipment = @json($data['equipment']);
    const allParts = @json($data['all_parts'] ?? []);
    let selectedEquipmentId = null;
    let selectedGroupId = null;
    let selectedGroupData = null;
    let originalEquipmentValues = {};

    function selectEquipment(id, row) {
        selectedEquipmentId = id;
        selectedGroupId = null;

        // Highlight equipment row
        document.querySelectorAll('#equipment-table tr').forEach(r => r.classList.remove('selected'));
        row.classList.add('selected');

        // Find equipment data
        const equipmentData = equipment.find(e => e.id === id);
        const groupsBody = document.getElementById('groups-body');
        groupsBody.innerHTML = '';

        if (equipmentData && equipmentData.part_groups && equipmentData.part_groups.length > 0) {
            equipmentData.part_groups.forEach(group => {
                const unitPriceSum = group.parts ? group.parts.reduce((sum, part) => {
                    if(part.unit_price && part.pivot.quantity){
                        return sum + ( part.pivot.quantity > 1 ? part.pivot.quantity * parseFloat(part.unit_price) : parseFloat(part.unit_price));
                    }
                    return sum;
                }, 0) : 0;

                const tr = document.createElement('tr');
                tr.onclick = () => selectGroup(group.id, tr, equipmentData);
                tr.style.cursor = 'pointer';
                tr.setAttribute('data-group-id', group.id);
                tr.innerHTML = `
                    <td class="editable" data-field="name" data-group-id="${group.id}">${group.name}</td>
                    <td>${group.total_price ? parseFloat(group.total_price).toFixed(2) : unitPriceSum.toFixed(2)}</td>
                    <td class="editable" data-field="notes" data-group-id="${group.id}">${group.notes || '-'}</td>
                    <td>
                        <button onclick="editGroup(${group.id}, event)" class="text-blue-600 hover:underline edit-btn">Edit</button>
                        <button onclick="saveGroup(${group.id}, event)" class="text-green-600 hover:underline save-btn" style="display:none;">Save</button>
                        <button onclick="cancelEditGroup(${group.id}, event)" class="text-gray-600 hover:underline cancel-btn" style="display:none;">Cancel</button>
                        <button onclick="deleteGroup(${group.id}, event)" class="text-red-600 hover:underline">Delete</button>
                    </td>
                `;
                groupsBody.appendChild(tr);
            });
        }

        // Add inline form for new group
        const addGroupRow = document.createElement('tr');
        addGroupRow.innerHTML = `
            <td><input type="text" id="new-group-name" placeholder="Group Name" class="border p-1 w-full" required></td>
            <td>-</td>
            <td><input type="text" id="new-group-notes" placeholder="Notes" class="border p-1 w-full"></td>
            <td><button onclick="addGroup()" class="text-green-600 font-bold">Add</button></td>
        `;
        groupsBody.appendChild(addGroupRow);

        // Clear parts table
        document.getElementById('parts-body').innerHTML = '<tr><td colspan="7" class="text-center text-muted">Select group to view parts</td></tr>';
    }

    function selectGroup(groupId, row, equipmentData) {
        selectedGroupId = groupId;
        selectedGroupData = equipmentData.part_groups.find(g => g.id === groupId);

        // Highlight group row
        document.querySelectorAll('#groups-table tr').forEach(r => r.classList.remove('selected'));
        row.classList.add('selected');

        const partsBody = document.getElementById('parts-body');
        partsBody.innerHTML = '';

        if (selectedGroupData && selectedGroupData.parts && selectedGroupData.parts.length > 0) {
            selectedGroupData.parts.forEach(part => {
                const tr = document.createElement('tr');
                tr.innerHTML = `
                    <td>${part.code || '-'}</td>
                    <td>${part.name}</td>
                    <td>${part.type || '-'}</td>
                    <td>${part.pivot.quantity || part.quantity}</td>
                    <td>${part.unit_price ? parseFloat(part.unit_price).toFixed(2) : ''}</td>
                    <td>${part.measure_unit}</td>
                    <td>
                        <button onclick="removePart(${part.id}, event)" class="text-red-600 hover:underline">Remove</button>
                    </td>
                `;
                partsBody.appendChild(tr);
            });
        }

        // Add inline form for new part
        let partOptions = '<option value="">Select Part</option>';
        allParts.forEach(part => {
            partOptions += `<option value="${part.id}">${part.code || 'No Code'} ${part.name} </option>`;
        });

        const addPartRow = document.createElement('tr');
        addPartRow.innerHTML = `
            <td><select id="new-part-select" class="border p-1 w-full" required>${partOptions}</select></td>
            <td>-</td>
            <td>-</td>
            <td><input type="number" id="new-part-quantity" placeholder="Quantity" class="border p-1 w-full" min="1" required></td>
            <td>-</td>
            <td>-</td>
            <td><button onclick="addPart()" class="text-green-600 font-bold">Add</button></td>
        `;
        partsBody.appendChild(addPartRow);
    }

    // Group management functions
    let originalGroupValues = {};

    function editGroup(id, event) {
        event.stopPropagation();
        const row = event.target.closest('tr');
        const editableCells = row.querySelectorAll('.editable');

        originalGroupValues[id] = {};
        editableCells.forEach(cell => {
            const field = cell.dataset.field;
            let value = cell.textContent === '-' ? '' : cell.textContent;
            originalGroupValues[id][field] = cell.textContent;

            const input = document.createElement('input');
            input.type = 'text';
            input.value = value;
            input.className = 'border p-1 w-full';
            input.dataset.field = field;

            cell.innerHTML = '';
            cell.appendChild(input);
        });

        row.querySelector('.edit-btn').style.display = 'none';
        row.querySelector('.save-btn').style.display = 'inline';
        row.querySelector('.cancel-btn').style.display = 'inline';
    }

    function saveGroup(id, event) {
        event.stopPropagation();
        const row = event.target.closest('tr');
        const inputs = row.querySelectorAll('input[data-field]');

        const formData = new FormData();
        formData.append('_token', '{{ csrf_token() }}');
        formData.append('_method', 'PATCH');

        inputs.forEach(input => {
            formData.append(input.dataset.field, input.value);
        });

        fetch(`/equipment-part-groups/${id}`, {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            inputs.forEach(input => {
                const cell = input.parentElement;
                let displayValue = input.value || '-';
                cell.textContent = displayValue;
            });

            row.querySelector('.edit-btn').style.display = 'inline';
            row.querySelector('.save-btn').style.display = 'none';
            row.querySelector('.cancel-btn').style.display = 'none';
            refreshEquipmentData();
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Failed to update group. Please check your input and try again.');
        });
    }

    function cancelEditGroup(id, event) {
        event.stopPropagation();
        const row = event.target.closest('tr');
        const editableCells = row.querySelectorAll('.editable');

        editableCells.forEach(cell => {
            const field = cell.dataset.field;
            cell.textContent = originalGroupValues[id][field];
        });

        row.querySelector('.edit-btn').style.display = 'inline';
        row.querySelector('.save-btn').style.display = 'none';
        row.querySelector('.cancel-btn').style.display = 'none';
    }

    function deleteGroup(id, event) {
        event.stopPropagation();
        if (!confirm('Are you sure you want to delete this group?')) return;

        fetch(`/equipment-part-groups/${id}`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Content-Type': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            refreshEquipmentData();
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Failed to delete group. Please try again.');
        });
    }

    function addGroup() {
        const name = document.getElementById('new-group-name').value;
        const notes = document.getElementById('new-group-notes').value;

        if (!name) {
            alert('Group name is required');
            return;
        }

        const formData = new FormData();
        formData.append('_token', '{{ csrf_token() }}');
        formData.append('equipment_id', selectedEquipmentId);
        formData.append('name', name);
        formData.append('notes', notes);

        fetch('/equipment-part-groups', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            document.getElementById('new-group-name').value = '';
            document.getElementById('new-group-notes').value = '';
            refreshEquipmentData();
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Failed to add group. Please check that the group name is unique and try again.');
        });
    }

    function addPart() {
        const partId = document.getElementById('new-part-select').value;
        const quantity = document.getElementById('new-part-quantity').value;

        if (!partId || !quantity) {
            alert('Part and quantity are required');
            return;
        }

        const formData = new FormData();
        formData.append('_token', '{{ csrf_token() }}');
        formData.append('part_id', partId);
        formData.append('quantity', quantity);

        fetch(`/equipment-part-groups/${selectedGroupId}/parts`, {
            method: 'POST',
            body: formData
        })
        .then(response => {
            if (response.ok) {
                return response.json();
            }
            throw new Error('Part already exists in group');
        })
        .then(data => {
            document.getElementById('new-part-select').value = '';
            document.getElementById('new-part-quantity').value = '';
            refreshEquipmentData();
        })
        .catch(error => {
            console.error('Error:', error);
            const selectedPart = allParts.find(p => p.id == partId);
            const partInfo = selectedPart ? `${selectedPart.code || 'No Code'} - ${selectedPart.name}` : 'Selected part';
            alert(`Part already exists in this group: ${partInfo}`);
        });
    }

    function removePart(partId, event) {
        event.stopPropagation();
        if (!confirm('Are you sure you want to remove this part from the group?')) return;

        fetch(`/equipment-part-groups/${selectedGroupId}/parts/${partId}`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Content-Type': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            refreshEquipmentData();
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Failed to remove part from group. Please try again.');
        });
    }

    // Equipment management functions
    function editEquipment(id, event) {
        event.stopPropagation();
        const row = document.getElementById(`equipment-row-${id}`);
        const editableCells = row.querySelectorAll('.editable');

        originalEquipmentValues[id] = {};
        editableCells.forEach(cell => {
            const field = cell.dataset.field;
            let value = cell.textContent;
            originalEquipmentValues[id][field] = cell.textContent;

            const input = document.createElement('input');
            input.type = field === 'stug_price' ? 'number' : 'text';
            if (field === 'stug_price') input.step = '0.01';
            input.value = value;
            input.className = 'border p-1 w-full';
            input.dataset.field = field;

            cell.innerHTML = '';
            cell.appendChild(input);
        });

        row.classList.add('editing');
        row.querySelector('.edit-btn').style.display = 'none';
        row.querySelector('.save-btn').style.display = 'inline';
        row.querySelector('.cancel-btn').style.display = 'inline';
    }

    function saveEquipment(id, event) {
        event.stopPropagation();
        const row = document.getElementById(`equipment-row-${id}`);
        const inputs = row.querySelectorAll('input[data-field]');

        const formData = new FormData();
        formData.append('_token', '{{ csrf_token() }}');
        formData.append('_method', 'PATCH');

        inputs.forEach(input => {
            formData.append(input.dataset.field, input.value);
        });

        fetch(`/equipment/${id}`, {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            inputs.forEach(input => {
                const cell = input.parentElement;
                let displayValue = input.value;
                if (input.dataset.field === 'stug_price') {
                    displayValue = parseFloat(input.value).toFixed(2);
                }
                cell.textContent = displayValue;
            });

            row.classList.remove('editing');
            row.querySelector('.edit-btn').style.display = 'inline';
            row.querySelector('.save-btn').style.display = 'none';
            row.querySelector('.cancel-btn').style.display = 'none';
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Failed to update equipment. Please check your input and try again.');
        });
    }

    function cancelEditEquipment(id, event) {
        event.stopPropagation();
        const row = document.getElementById(`equipment-row-${id}`);
        const editableCells = row.querySelectorAll('.editable');

        editableCells.forEach(cell => {
            const field = cell.dataset.field;
            cell.textContent = originalEquipmentValues[id][field];
        });

        row.classList.remove('editing');
        row.querySelector('.edit-btn').style.display = 'inline';
        row.querySelector('.save-btn').style.display = 'none';
        row.querySelector('.cancel-btn').style.display = 'none';
    }

    function handleEquipmentSubmit(event) {
        // Let the form submit normally, it will redirect back to counters tab
        return true;
    }

    function refreshEquipmentData() {
        // Store current selections
        const currentEquipmentId = selectedEquipmentId;
        const currentGroupId = selectedGroupId;

        // Reload the page but maintain selections via URL hash
        if (currentEquipmentId && currentGroupId) {
            window.location.hash = `equipment-${currentEquipmentId}-group-${currentGroupId}`;
        } else if (currentEquipmentId) {
            window.location.hash = `equipment-${currentEquipmentId}`;
        }
        location.reload();
    }

    // Restore selections on page load
    window.addEventListener('load', function() {
        if (window.location.hash) {
            const hash = window.location.hash.substring(1);
            const equipmentMatch = hash.match(/equipment-(\d+)/);
            const groupMatch = hash.match(/group-(\d+)/);

            if (equipmentMatch) {
                const equipmentId = parseInt(equipmentMatch[1]);
                const equipmentRow = document.querySelector(`[data-id="${equipmentId}"]`);
                if (equipmentRow) {
                    selectEquipment(equipmentId, equipmentRow);

                    if (groupMatch) {
                        const groupId = parseInt(groupMatch[1]);
                        setTimeout(() => {
                            const groupRow = document.querySelector(`[data-group-id="${groupId}"]`);
                            if (groupRow) {
                                const equipmentData = equipment.find(e => e.id === equipmentId);
                                selectGroup(groupId, groupRow, equipmentData);
                            }
                        }, 100);
                    }
                }
            }
            // Clear hash after restoration
            window.location.hash = '';
        }
    });
    @endif
</script>

<style>
.selected {
    background-color: #e3f2fd !important;
}
</style>
