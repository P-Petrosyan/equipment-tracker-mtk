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
                    <tr>
                        <td>{{ $defect->group_id }}</td>
                        <td>{{ $defect->id }}</td>
                        <td>{{ $defect->description }}</td>
                        <td>{{ $defect->note }}</td>
                        <td>
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
