<table>
    <thead>
        <tr>
            <th>Code</th>
            <th>Name</th>
            <th>Price</th>
            <th>Quantity</th>
            <th>Measure Unit</th>
        </tr>
    </thead>
    <tbody>
        @foreach($parts as $part)
            <tr>
                <td>{{ $part->code }}</td>
                <td>{{ $part->name }}</td>
                <td>{{ $part->unit_price }}</td>
                <td>{{ $part->quantity ?? 0 }}</td>
                <td>{{ $part->measure_unit }}</td>
            </tr>
        @endforeach
    </tbody>
</table>
