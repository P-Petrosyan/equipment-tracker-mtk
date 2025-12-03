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
                    <tr>
                        <td>{{ $position->title }}</td>
                        <td>{{ $position->titleholder }}</td>
                        <td>{{ $position->note }}</td>
                        <td>
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