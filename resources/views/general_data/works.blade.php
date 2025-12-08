<div class="section-header">Աշխատանքներ (Works)</div>
<div class="data-table-wrapper">
    <table class="ms-table">
        <thead>
            <tr>
                <th>Ստացման ամսաթիվ</th>
                <th>Exit Date</th>
                <th>Partner</th>
                <th>Structure</th>
                <th>Equipment</th>
                <th>Old Serial</th>
                <th>New Serial</th>
                <th>Representative</th>
                <th>Non Repairable</th>
                <th>Conclusion #</th>
                <th>Group Total Price</th>
                <th>Status</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @if(isset($data['works']))
                @foreach($data['works'] as $work)
                    <tr id="work-row-{{ $work->id }}">
                        <td class="editable" data-field="receive_date">{{ $work->receive_date }}</td>
                        <td class="editable" data-field="exit_date">{{ $work->exit_date ?? '-' }}</td>
                        <td>{{ $work->partner->name ?? '-' }}</td>
                        <td>{{ $work->partnerStructure->name ?? '-' }}</td>
                        <td>{{ $work->equipment->name ?? '-' }}</td>
                        <td class="editable" data-field="old_serial_number">{{ $work->old_serial_number ?? '-' }}</td>
                        <td class="editable" data-field="new_serial_number">{{ $work->new_serial_number ?? '-' }}</td>
                        <td class="editable" data-field="partner_representative">{{ $work->partner_representative ?? '-' }}</td>
                        <td>{{ $work->non_repairable ? 'Yes' : 'No' }}</td>
                        <td class="editable" data-field="conclusion_number">{{ $work->conclusion_number ?? '-' }}</td>
                        <td class="editable" data-field="equipment_part_group_total_price">{{ $work->equipment_part_group_total_price ?? '-' }}</td>
                        <td>{{ $work->status ? 'Active' : 'Inactive' }}</td>
                        <td>
                            <button onclick="editWork({{ $work->id }}, event)" class="text-blue-600 hover:underline edit-btn">Edit</button>
                            <button onclick="saveWork({{ $work->id }}, event)" class="text-green-600 hover:underline save-btn" style="display:none;">Save</button>
                            <button onclick="cancelEditWork({{ $work->id }}, event)" class="text-gray-600 hover:underline cancel-btn" style="display:none;">Cancel</button>
                            <form action="{{ route('works.destroy', $work) }}" method="POST" onsubmit="return confirm('Are you sure?');" style="display:inline;">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-red-600 hover:underline">Delete</button>
                            </form>
                        </td>
                    </tr>
                @endforeach
            @endif
            <tr>
                <form action="{{ route('works.store') }}" method="POST">
                    @csrf
                    <td>
                        <input type="date" name="receive_date" class="border p-1 w-full" required>
                    </td>
                    <td>
                        <input type="date" name="exit_date" class="border p-1 w-full">
                    </td>
                    <td>
                        <select name="partner_id" class="border p-1 w-full" required>
                            <option value="">Select Partner</option>
                            @if(isset($data['partners']))
                                @foreach($data['partners'] as $partner)
                                    <option value="{{ $partner->id }}">{{ $partner->name }}</option>
                                @endforeach
                            @endif
                        </select>
                    </td>
                    <td>
                        <select name="partner_structure_id" class="border p-1 w-full">
                            <option value="">Select Structure</option>
                        </select>
                    </td>
                    <td>
                        <select name="equipment_id" class="border p-1 w-full" required>
                            <option value="">Select Equipment</option>
                            @if(isset($data['equipment']))
                                @foreach($data['equipment'] as $equipment)
                                    <option value="{{ $equipment->id }}">{{ $equipment->name }}</option>
                                @endforeach
                            @endif
                        </select>
                    </td>
                    <td>
                        <input type="text" name="old_serial_number" placeholder="Old Serial" class="border p-1 w-full">
                    </td>
                    <td>
                        <input type="text" name="new_serial_number" placeholder="New Serial" class="border p-1 w-full">
                    </td>
                    <td>
                        <input type="text" name="partner_representative" placeholder="Representative" class="border p-1 w-full">
                    </td>
                    <td>
                        <input type="checkbox" name="non_repairable" value="1">
                    </td>
                    <td>
                        <input type="text" name="conclusion_number" placeholder="Conclusion #" class="border p-1 w-full">
                    </td>
                    <td>
                        <input type="number" step="0.01" name="equipment_part_group_total_price" placeholder="Total Price" class="border p-1 w-full">
                    </td>
                    <td>
                        <select name="status" class="border p-1 w-full">
                            <option value="0">Inactive</option>
                            <option value="1">Active</option>
                        </select>
                    </td>
                    <td>
                        <button type="submit" class="text-green-600 font-bold">Add</button>
                    </td>
                </form>
            </tr>
        </tbody>
    </table>
</div>

<script>
    @if(isset($data['works']))
    let originalWorkValues = {};

    function editWork(id, event) {
        event.stopPropagation();
        const row = document.getElementById(`work-row-${id}`);
        const editableCells = row.querySelectorAll('.editable');

        originalWorkValues[id] = {};
        editableCells.forEach(cell => {
            const field = cell.dataset.field;
            let value = cell.textContent === '-' ? '' : cell.textContent;
            originalWorkValues[id][field] = cell.textContent;

            const input = document.createElement('input');
            input.type = ['receive_date', 'exit_date'].includes(field) ? 'date' :
                        field === 'equipment_part_group_total_price' ? 'number' : 'text';
            if (field === 'equipment_part_group_total_price') input.step = '0.01';
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

    function saveWork(id, event) {
        event.stopPropagation();
        const row = document.getElementById(`work-row-${id}`);
        const inputs = row.querySelectorAll('input[data-field]');

        const formData = new FormData();
        formData.append('_token', '{{ csrf_token() }}');
        formData.append('_method', 'PATCH');

        inputs.forEach(input => {
            formData.append(input.dataset.field, input.value);
        });

        fetch(`/works/${id}`, {
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
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Failed to update work. Please check your input and try again.');
        });
    }

    function cancelEditWork(id, event) {
        event.stopPropagation();
        const row = document.getElementById(`work-row-${id}`);
        const editableCells = row.querySelectorAll('.editable');

        editableCells.forEach(cell => {
            const field = cell.dataset.field;
            cell.textContent = originalWorkValues[id][field];
        });

        row.querySelector('.edit-btn').style.display = 'inline';
        row.querySelector('.save-btn').style.display = 'none';
        row.querySelector('.cancel-btn').style.display = 'none';
    }
    @endif
</script>
