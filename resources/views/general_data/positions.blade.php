<div class="section-header">Պաշտոններ (Positions)</div>
<div class="data-table-wrapper">
    <table class="ms-table">
        <thead>
            <tr>
                <th>Title</th>
                <th>Titleholder</th>
                <th>Note</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @if(isset($data['positions']))
                @foreach($data['positions'] as $position)
                    <tr id="position-row-{{ $position->id }}">
                        <td class="editable" data-field="title">{{ $position->title }}</td>
                        <td class="editable" data-field="titleholder">{{ $position->titleholder }}</td>
                        <td class="editable" data-field="note">{{ $position->note }}</td>
                        <td>
                            <button onclick="editPosition({{ $position->id }}, event)" class="text-blue-600 hover:underline edit-btn">Edit</button>
                            <button onclick="savePosition({{ $position->id }}, event)" class="text-green-600 hover:underline save-btn" style="display:none;">Save</button>
                            <button onclick="cancelEdit({{ $position->id }}, event)" class="text-gray-600 hover:underline cancel-btn" style="display:none;">Cancel</button>
                            <form action="{{ route('positions.destroy', $position) }}" method="POST" onsubmit="return confirm('Are you sure?');" style="display:inline;">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-red-600 hover:underline">Delete</button>
                            </form>
                        </td>
                    </tr>
                @endforeach
            @endif
            <tr>
                <form action="{{ route('positions.store') }}" method="POST">
                    @csrf
                    <td>
                        <input type="text" name="title" placeholder="Title" class="border p-1 w-full" required>
                    </td>
                    <td>
                        <input type="text" name="titleholder" placeholder="Titleholder" class="border p-1 w-full">
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
@if(isset($data['positions']))
let originalValues = {};

function editPosition(id, event) {
    event.stopPropagation();
    const row = document.getElementById(`position-row-${id}`);
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

function savePosition(id, event) {
    event.stopPropagation();
    const row = document.getElementById(`position-row-${id}`);
    const inputs = row.querySelectorAll('input[data-field]');

    const formData = new FormData();
    formData.append('_token', '{{ csrf_token() }}');
    formData.append('_method', 'PATCH');

    inputs.forEach(input => {
        formData.append(input.dataset.field, input.value);
    });

    fetch(`/positions/${id}`, {
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
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Error updating position');
    });
}

function cancelEdit(id, event) {
    event.stopPropagation();
    const row = document.getElementById(`position-row-${id}`);
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
@endif
</script>