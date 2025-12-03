<div class="section-header">Դետալներ (Parts)</div>
<div class="data-table-wrapper">
    <table class="ms-table">
        <thead>
            <tr>
                <th>Code</th>
                <th>Type</th>
                <th>Name</th>
                <th>Price</th>
                <th>Drawing Number</th>
                <th>Quantity</th>
                <th>Measure Unit</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @if(isset($data['parts']))
                @foreach($data['parts'] as $part)
                    <tr id="part-row-{{ $part->id }}">
                        <td class="editable" data-field="code">{{ $part->code }}</td>
                        <td class="editable" data-field="type">{{ $part->type }}</td>
                        <td class="editable" data-field="name">{{ $part->name }}</td>
                        <td class="editable" data-field="unit_price">{{ $part->unit_price }}</td>
                        <td class="editable" data-field="drawing_number">{{ $part->drawing_number }}</td>
                        <td data-field="quantity">{{ $part->quantity?? 0 }}</td>
                        <td class="editable" data-field="measure_unit">{{ $part->measure_unit }}</td>
                        <td>
                            <button onclick="editOrderPart({{ $part->id }}, event)" class="text-blue-600 hover:underline edit-btn">Edit</button>
                            <button onclick="saveOrderPart({{ $part->id }}, event)" class="text-green-600 hover:underline save-btn" style="display:none;">Save</button>
                            <button onclick="cancelEditOrderPart({{ $part->id }}, event)" class="text-gray-600 hover:underline cancel-btn" style="display:none;">Cancel</button>
                            <form action="{{ route('parts.destroy', $part) }}" method="POST" onsubmit="return confirm('Are you sure?');" style="display:inline;">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-red-600 hover:underline">Delete</button>
                            </form>
                        </td>
                    </tr>
                @endforeach
            @endif
            <tr>
                <form action="{{ route('parts.store') }}" method="POST">
                    @csrf
                    <td>
                        <input type="text" name="code" placeholder="Code" class="border p-1 w-full" required>
                    </td>
                    <td>
                        <input type="text" name="type" placeholder="Type" class="border p-1 w-full" required>
                    </td>
                    <td>
                        <input type="text" name="name" placeholder="Name" class="border p-1 w-full" required>
                    </td>
                    <td>
                        <input type="number" step="0.01" name="unit_price" placeholder="Price" class="border p-1 w-full" required>
                    </td>
                    <td>
                        <input type="text" name="drawing_number" placeholder="Drawing Number" class="border p-1 w-full">
                    </td>
                    <td>
                        <input type="number" name="quantity" placeholder="Quantity" class="border p-1 w-full">
                    </td>
                    <td>
                        <input type="text" name="measure_unit" placeholder="Measure Unit" class="border p-1 w-full" value="հատ" required>
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
    @if(isset($data['parts']))
    let originalOrderPartValues = {};

    function editOrderPart(id, event) {
        event.stopPropagation();
        const row = document.getElementById(`part-row-${id}`);
        const editableCells = row.querySelectorAll('.editable');

        // Store original values
        originalOrderPartValues[id] = {};
        editableCells.forEach(cell => {
            const field = cell.dataset.field;
            let value = cell.textContent;

            // Remove $ sign from unit_price for editing
            // if (field === 'unit_price') {
            //     value = value.replace('$', '').replace(',', '');
            // }
            // Replace '-' with empty string for drawing_number
            if (field === 'drawing_number' && value === '-') {
                value = '';
            }

            originalOrderPartValues[id][field] = cell.textContent;

            const input = document.createElement('input');
            input.type = (field === 'unit_price' || field === 'quantity') ? 'number' : 'text';
            if (field === 'unit_price') input.step = '0.01';
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

    function saveOrderPart(id, event) {
        event.stopPropagation();
        const row = document.getElementById(`part-row-${id}`);
        const inputs = row.querySelectorAll('input[data-field]');

        const formData = new FormData();
        formData.append('_token', '{{ csrf_token() }}');
        formData.append('_method', 'PATCH');

        inputs.forEach(input => {
            formData.append(input.dataset.field, input.value);
        });

        fetch(`/parts/${id}`, {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            // Update display values
            inputs.forEach(input => {
                const cell = input.parentElement;
                let displayValue = input.value;

                if (input.dataset.field === 'unit_price') {
                    displayValue = parseFloat(input.value).toFixed(2);
                }
                // // Show '-' for empty drawing_number
                // if (input.dataset.field === 'drawing_number' && !input.value) {
                //     displayValue = '-';
                // }

                cell.textContent = displayValue;
            });

            row.classList.remove('editing');
            row.querySelector('.edit-btn').style.display = 'inline';
            row.querySelector('.save-btn').style.display = 'none';
            row.querySelector('.cancel-btn').style.display = 'none';
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Error updating part');
        });
    }

    function cancelEditOrderPart(id, event) {
        event.stopPropagation();
        const row = document.getElementById(`part-row-${id}`);
        const editableCells = row.querySelectorAll('.editable');

        // Restore original values
        editableCells.forEach(cell => {
            const field = cell.dataset.field;
            cell.textContent = originalOrderPartValues[id][field];
        });

        row.classList.remove('editing');
        row.querySelector('.edit-btn').style.display = 'inline';
        row.querySelector('.save-btn').style.display = 'none';
        row.querySelector('.cancel-btn').style.display = 'none';
    }
    @endif
</script>
