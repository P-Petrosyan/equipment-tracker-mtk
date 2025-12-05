<div class="section-header">Թերություններ (Defects)</div>
<div class="data-table-wrapper">
    <table class="ms-table">
        <thead>
            <tr>
                <th>Group ID</th>
                <th>ID</th>
                <th>Description</th>
                <th>Note</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @if(isset($data['defects']))
                @foreach($data['defects'] as $defect)
                    <tr id="defect-row-{{ $defect->id }}">
                        <td class="editable" data-field="group_id">{{ $defect->group_id }}</td>
                        <td>{{ $defect->id }}</td>
                        <td class="editable" data-field="description">{{ $defect->description }}</td>
                        <td class="editable" data-field="note">{{ $defect->note }}</td>
                        <td>
                            <button onclick="editDefect({{ $defect->id }}, event)" class="text-blue-600 hover:underline edit-btn">Edit</button>
                            <button onclick="saveDefect({{ $defect->id }}, event)" class="text-green-600 hover:underline save-btn" style="display:none;">Save</button>
                            <button onclick="cancelEdit({{ $defect->id }}, event)" class="text-gray-600 hover:underline cancel-btn" style="display:none;">Cancel</button>
                            <form action="{{ route('defects.destroy', $defect) }}" method="POST" onsubmit="return confirm('Are you sure?');" style="display:inline;">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-red-600 hover:underline">Delete</button>
                            </form>
                        </td>
                    </tr>
                @endforeach
            @endif
            <tr>
                <form action="{{ route('defects.store') }}" method="POST">
                    @csrf
                    <td>
                        <input type="text" name="group_id" placeholder="Group ID" class="border p-1 w-full" required>
                    </td>
                    <td></td>
                    <td>
                        <input type="text" name="description" placeholder="Description" class="border p-1 w-full" required>
                    </td>
                    <td>
                        <input type="text" name="note" placeholder="Note" class="border p-1 w-full">
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
@if(isset($data['defects']))
let originalValues = {};

function editDefect(id, event) {
    event.stopPropagation();
    const row = document.getElementById(`defect-row-${id}`);
    const editableCells = row.querySelectorAll('.editable');

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

function saveDefect(id, event) {
    event.stopPropagation();
    const row = document.getElementById(`defect-row-${id}`);
    const inputs = row.querySelectorAll('input[data-field]');

    const formData = new FormData();
    formData.append('_token', '{{ csrf_token() }}');
    formData.append('_method', 'PATCH');

    inputs.forEach(input => {
        formData.append(input.dataset.field, input.value);
    });

    fetch(`/defects/${id}`, {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        inputs.forEach(input => {
            const cell = input.parentElement;
            cell.textContent = input.value;
        });

        row.classList.remove('editing');
        row.querySelector('.edit-btn').style.display = 'inline';
        row.querySelector('.save-btn').style.display = 'none';
        row.querySelector('.cancel-btn').style.display = 'none';
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Error updating defect');
    });
}

function cancelEdit(id, event) {
    event.stopPropagation();
    const row = document.getElementById(`defect-row-${id}`);
    const editableCells = row.querySelectorAll('.editable');

    editableCells.forEach(cell => {
        const field = cell.dataset.field;
        cell.textContent = originalValues[id][field];
    });

    row.classList.remove('editing');
    row.querySelector('.edit-btn').style.display = 'inline';
    row.querySelector('.save-btn').style.display = 'none';
    row.querySelector('.cancel-btn').style.display = 'none';
}
@endif
</script>
