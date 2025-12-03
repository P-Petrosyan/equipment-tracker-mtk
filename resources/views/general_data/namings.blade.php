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
                    <tr>
                        <td>{{ $naming->id }}</td>
                        <td>{{ $naming->name }}</td>
                        <td>{{ $naming->text }}</td>
                        <td>
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
