<div class="section-header">Եզրակացության պատճառները (Conclusion Reasons)</div>
<div class="data-table-wrapper">
    <table class="ms-table">
        <thead>
            <tr>
                <th>Reason Id</th>
                <th>Reason</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @if(isset($data['reasons']))
                @foreach($data['reasons'] as $reason)
                    <tr id="reason-row-{{ $reason->id }}">
                        <td>{{ $reason->id }}</td>
                        <td class="editable" data-field="name">{{ $reason->name }}</td>
                        <td>
                            <button onclick="editReason({{ $reason->id }}, event)" class="text-blue-600 hover:underline edit-btn">Edit</button>
                            <button onclick="saveReason({{ $reason->id }}, event)" class="text-green-600 hover:underline save-btn" style="display:none;">Save</button>
                            <button onclick="cancelEdit({{ $reason->id }}, event)" class="text-gray-600 hover:underline cancel-btn" style="display:none;">Cancel</button>
                            <form action="{{ route('reasons.destroy', $reason) }}" method="POST" onsubmit="return confirm('Are you sure?');" style="display:inline;">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-red-600 hover:underline">Delete</button>
                            </form>
                        </td>
                    </tr>
                @endforeach
            @endif
            <tr>
                <td></td>
                <td>
                    <form action="{{ route('reasons.store') }}" method="POST" style="display:inline;">
                        @csrf
                        <input type="text" name="name" placeholder="Reason name" class="border p-1 w-full" required>
                </td>
                <td>
                        <button type="submit" class="text-green-600 font-bold">Add</button>
                    </form>
                </td>
            </tr>
        </tbody>
    </table>
</div>

<script>
@if(isset($data['reasons']))
let originalValues = {};

function editReason(id, event) {
    event.stopPropagation();
    const row = document.getElementById(`reason-row-${id}`);
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

function saveReason(id, event) {
    event.stopPropagation();
    const row = document.getElementById(`reason-row-${id}`);
    const inputs = row.querySelectorAll('input[data-field]');

    const formData = new FormData();
    formData.append('_token', '{{ csrf_token() }}');
    formData.append('_method', 'PATCH');

    inputs.forEach(input => {
        formData.append(input.dataset.field, input.value);
    });

    fetch(`/reasons/${id}`, {
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
        alert('Error updating reason');
    });
}

function cancelEdit(id, event) {
    event.stopPropagation();
    const row = document.getElementById(`reason-row-${id}`);
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
