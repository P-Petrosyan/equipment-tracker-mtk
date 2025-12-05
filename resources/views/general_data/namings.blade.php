<div class="section-header">Անվանումներ (Namings)</div>
<div class="data-table-wrapper">
    <table class="ms-table">
        <thead>
            <tr>
                <th>ID</th>
                <th>Name</th>
                <th>Text</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @if(isset($data['namings']))
                @foreach($data['namings'] as $naming)
                    <tr id="naming-row-{{ $naming->id }}">
                        <td>{{ $naming->id }}</td>
                        <td class="editable" data-field="name">{{ $naming->name }}</td>
                        <td class="editable" data-field="text">{{ $naming->text }}</td>
                        <td>
                            <button onclick="editNaming({{ $naming->id }}, event)" class="text-blue-600 hover:underline edit-btn">Edit</button>
                            <button onclick="saveNaming({{ $naming->id }}, event)" class="text-green-600 hover:underline save-btn" style="display:none;">Save</button>
                            <button onclick="cancelEdit({{ $naming->id }}, event)" class="text-gray-600 hover:underline cancel-btn" style="display:none;">Cancel</button>
                            <form action="{{ route('namings.destroy', $naming) }}" method="POST" onsubmit="return confirm('Are you sure?');" style="display:inline;">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-red-600 hover:underline">Delete</button>
                            </form>
                        </td>
                    </tr>
                @endforeach
            @endif
            <tr>
                <form action="{{ route('namings.store') }}" method="POST">
                    @csrf
                    <td></td>
                    <td>
                        <input type="text" name="name" placeholder="Name" class="border p-1 w-full" required>
                    </td>
                    <td>
                        <input type="text" name="text" placeholder="Text" class="border p-1 w-full">
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
@if(isset($data['namings']))
let originalValues = {};

function editNaming(id, event) {
    event.stopPropagation();
    const row = document.getElementById(`naming-row-${id}`);
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

function saveNaming(id, event) {
    event.stopPropagation();
    const row = document.getElementById(`naming-row-${id}`);
    const inputs = row.querySelectorAll('input[data-field]');

    const formData = new FormData();
    formData.append('_token', '{{ csrf_token() }}');
    formData.append('_method', 'PATCH');

    inputs.forEach(input => {
        formData.append(input.dataset.field, input.value);
    });

    fetch(`/namings/${id}`, {
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
        alert('Error updating naming');
    });
}

function cancelEdit(id, event) {
    event.stopPropagation();
    const row = document.getElementById(`naming-row-${id}`);
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
