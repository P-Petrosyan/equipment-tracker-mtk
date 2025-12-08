<div class="section-header">Գործընկերներ (Partners)</div>
<div class="data-table-wrapper" style="flex: 1;">
    <table class="ms-table" id="partners-table">
        <thead>
        <tr>
            <th>Տարածաշրջան</th>
            <th>Հասցե</th>
            <th>Տնօրեն</th>
            <th>Հաշվապահ</th>
            <th>Նշումներ</th>
            <th></th>
        </tr>
        </thead>
        <tbody>
        @if(isset($data['partners']))
            @foreach($data['partners'] as $partner)
                <tr onclick="selectPartner({{ $partner->id }}, this)" data-id="{{ $partner->id }}" id="partner-row-{{ $partner->id }}">
                    <td class="editable" data-field="region">{{ $partner->region }}</td>
                    <td class="editable" data-field="address">{{ $partner->address }}</td>
                    <td class="editable" data-field="tnoren">{{ $partner->tnoren }}</td>
                    <td class="editable" data-field="hashvapah">{{ $partner->hashvapah }}</td>
                    <td class="editable" data-field="notes">{{ $partner->notes }}</td>
                    <td>
                        <button onclick="editPartner({{ $partner->id }}, event)" class="text-blue-600 hover:underline edit-btn">Edit</button>
                        <button onclick="savePartner({{ $partner->id }}, event)" class="text-green-600 hover:underline save-btn" style="display:none;">Save</button>
                        <button onclick="cancelEdit({{ $partner->id }}, event)" class="text-gray-600 hover:underline cancel-btn" style="display:none;">Cancel</button>
                        <form action="{{ route('partners.destroy', $partner) }}" method="POST" onsubmit="return confirm('Are you sure?');" style="display:inline;">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="text-red-600 hover:underline">Delete</button>
                        </form>
                    </td>
                </tr>
            @endforeach
        @endif
        <tr>
            <form action="{{ route('partners.store') }}" method="POST" onsubmit="return handlePartnerSubmit(event)">
                @csrf
                <input type="hidden" name="redirect_to" value="partners">
                <td>
                    <input type="text" name="region" placeholder="Region" class="border p-1 w-full" required>
                </td>
                <td>
                    <input type="text" name="address" placeholder="Address" class="border p-1 w-full">
                </td>
                <td>
                    <input type="text" name="tnoren" placeholder="Tnoren" class="border p-1 w-full">
                </td>
                <td>
                    <input type="text" name="hashvapah" placeholder="Hashvapah" class="border p-1 w-full">
                </td>
                <td>
                    <input type="text" name="notes" placeholder="Notes" class="border p-1 w-full">
                </td>
                <td>
                    <button type="submit" class="text-green-600 font-bold">Add</button>
                </td>
            </form>
        </tr>
        </tbody>
    </table>
</div>

<div class="section-header mt-2">Ստորաբաժանումներ (SS)</div>
<div class="data-table-wrapper" style="height: 200px;">
    <table class="ms-table">
        <thead>
        <tr>
            <th>ՏՏ</th>
            <th>Actions</th>
        </tr>
        </thead>
        <tbody id="structures-body">
        <tr>
            <td colspan="2" class="text-center text-muted">Select a partner to view structures</td>
        </tr>
        </tbody>
    </table>
</div>

<script>
    @if(isset($data['partners']))
    const partners = @json($data['partners']);
    let originalValues = {};

    function selectPartner(id, row) {
        // Don't select if in edit mode
        if (row.classList.contains('editing')) return;

        // Highlight row
        document.querySelectorAll('#partners-table tr').forEach(r => r.classList.remove('selected'));
        row.classList.add('selected');

        // Populate structures
        const partner = partners.find(p => p.id === id);
        const tbody = document.getElementById('structures-body');
        tbody.innerHTML = '';

        if (partner && partner.structures && partner.structures.length > 0) {
            partner.structures.forEach(s => {
                const tr = document.createElement('tr');
                tr.innerHTML = `
                <td>${s.name}</td>
                <td>
                    <button onclick="deleteStructure(${s.id}, ${id}, event)" class="text-red-600 hover:underline">Delete</button>
                </td>
`;
                tbody.appendChild(tr);
            });
        } else {
            tbody.innerHTML = '<tr><td colspan="2" class="text-center text-muted">No structures found</td></tr>';
        }

        // Add "Add Structure" row
        const addTr = document.createElement('tr');
        addTr.innerHTML = `
            <td>
                <input type="text" id="new-structure-name-${id}" placeholder="New SS Name" class="border p-1 w-full">
            </td>
            <td>
                <button onclick="submitAddStructure(${id})" class="text-green-600 font-bold">Add</button>
            </td>
        `;
        tbody.appendChild(addTr);
    }

    function editPartner(id, event) {
        event.stopPropagation();
        const row = document.getElementById(`partner-row-${id}`);
        const editableCells = row.querySelectorAll('.editable');

        // Store original values
        originalValues[id] = {};
        editableCells.forEach(cell => {
            const field = cell.dataset.field;
            originalValues[id][field] = cell.textContent;

            const input = document.createElement('input');
            input.type = 'text';
            input.value = cell.textContent;
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

    function savePartner(id, event) {
        event.stopPropagation();
        const row = document.getElementById(`partner-row-${id}`);
        const inputs = row.querySelectorAll('input[data-field]');

        const formData = new FormData();
        formData.append('_token', '{{ csrf_token() }}');
        formData.append('_method', 'PATCH');

        inputs.forEach(input => {
            formData.append(input.dataset.field, input.value);
        });

        fetch(`/partners/${id}`, {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            // Update display values
            inputs.forEach(input => {
                const cell = input.parentElement;
                cell.textContent = input.value;
            });

            row.classList.remove('editing');
            row.querySelector('.edit-btn').style.display = 'inline';
            row.querySelector('.save-btn').style.display = 'none';
            row.querySelector('.cancel-btn').style.display = 'none';

            // Update partners array
            const partnerIndex = partners.findIndex(p => p.id === id);
            if (partnerIndex !== -1) {
                inputs.forEach(input => {
                    partners[partnerIndex][input.dataset.field] = input.value;
                });
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Error updating partner');
        });
    }

    function cancelEdit(id, event) {
        event.stopPropagation();
        const row = document.getElementById(`partner-row-${id}`);
        const editableCells = row.querySelectorAll('.editable');

        // Restore original values
        editableCells.forEach(cell => {
            const field = cell.dataset.field;
            cell.textContent = originalValues[id][field];
        });

        row.classList.remove('editing');
        row.querySelector('.edit-btn').style.display = 'inline';
        row.querySelector('.save-btn').style.display = 'none';
        row.querySelector('.cancel-btn').style.display = 'none';
    }

    function submitAddStructure(partnerId) {
        const nameInput = document.getElementById(`new-structure-name-${partnerId}`);
        const name = nameInput.value;
        if (!name) return;

        const formData = new FormData();
        formData.append('_token', '{{ csrf_token() }}');
        formData.append('partner_id', partnerId);
        formData.append('name', name);

        fetch('{{ route("partner-structures.store") }}', {
            method: 'POST',
            body: formData,
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json'
            }
        })
        .then(response => {
            if (response.ok) {
                nameInput.value = '';
                refreshPartnerData(partnerId);
            } else {
                throw new Error('Network response was not ok');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Error adding structure');
        });
    }

    function deleteStructure(structureId, partnerId, event) {
        event.preventDefault();
        if (!confirm('Are you sure?')) return;

        const formData = new FormData();
        formData.append('_token', '{{ csrf_token() }}');
        formData.append('_method', 'DELETE');

        fetch(`/partner-structures/${structureId}`, {
            method: 'POST',
            body: formData,
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json'
            }
        })
        .then(response => {
            if (response.ok) {
                refreshPartnerData(partnerId);
            } else {
                throw new Error('Network response was not ok');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Error deleting structure');
        });
    }

    function refreshPartnerData(partnerId) {
        // Store current selection
        window.location.hash = `partner-${partnerId}`;
        location.reload();
    }

    function handlePartnerSubmit(event) {
        // Let the form submit normally, it will redirect back to partners tab
        return true;
    }

    // Function to restore partner selection
    function restorePartnerSelection() {
        if (window.location.hash) {
            const hash = window.location.hash.substring(1);
            const partnerMatch = hash.match(/partner-(\d+)/);

            if (partnerMatch) {
                const partnerId = parseInt(partnerMatch[1]);
                const partnerRow = document.querySelector(`[data-id="${partnerId}"]`);
                if (partnerRow) {
                    selectPartner(partnerId, partnerRow);
                }
            }
            // Clear hash after restoration
            window.location.hash = '';
        }
    }

    // Restore partner selection on page load
    document.addEventListener('DOMContentLoaded', restorePartnerSelection);
    window.addEventListener('load', restorePartnerSelection);

    // Also check immediately in case DOM is already loaded
    if (document.readyState === 'loading') {
        // DOM is still loading
    } else {
        // DOM is already loaded
        restorePartnerSelection();
    }
    @endif
</script>
