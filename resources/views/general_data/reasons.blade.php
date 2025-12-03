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
                    <tr>
                        <td>{{ $reason->id }}</td>
                        <td>{{ $reason->name }}</td>
                        <td>
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
                        <input type="text" name="name" placeholder="New SS Name" class="border p-1 w-full" required>
                </td>
                <td>
                        <button type="submit" class="text-green-600 font-bold">Add</button>
                    </form>
                </td>
            </tr>
        </tbody>
    </table>
</div>
