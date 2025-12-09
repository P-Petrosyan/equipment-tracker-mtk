@extends('layouts.app')

@section('title', 'Աշխատանքներ')

@section('content')
    <style>
        .ms-table {
            border-collapse: collapse;
            width: 100%;
        }

        .ms-table th,
        .ms-table td {
            border: 1px solid #ddd;
            padding: 4px;
            text-align: left;
        }

        .ms-table th {
            background-color: #f2f2f2;
            /*font-weight: bold;*/
        }

        .ms-table tr:nth-child(even) {
            background-color: #f9f9f9;
        }
    </style>

    <div class="header">
        <h1 class="page-title" style="font-size: 1.2rem;">Աշխատանքներ (Works)</h1>
        <div style="display: flex; gap: 10px; align-items: center;">
            <button onclick="showTable('active')" id="active-btn" class="btn btn-m btn-primary">Ընթացիկ</button>
            <button onclick="showTable('archived')" id="archived-btn" class="btn btn-m btn-secondary">Արխիվ</button>

        </div>
        <a href="{{ route('dashboard') }}" class="btn btn-m btn-secondary">
            <i class="fa-solid fa-arrow-left"></i> Գլխավոր էջ
        </a>
    </div>

    <!-- Active Works Table -->
    <div id="active-table" class="data-table-wrapper">
        <h3>Ընթացիկ աշխատանքներ (Active Works)</h3>
{{--        <div style="display: flex; gap: 10px; margin-bottom: 10px; align-items: center;">--}}

            {{--        <form method="GET" style="display: flex; gap: 5px;">--}}
            {{--            <input type="text" name="old_serial" placeholder="Հին համար" value="{{ request('old_serial') }}" style="padding: 5px;">--}}
            {{--            <input type="text" name="new_serial" placeholder="Նոր համար" value="{{ request('new_serial') }}" style="padding: 5px;">--}}
            {{--            <button type="submit" class="btn btn-m btn-info">Որոնել</button>--}}
            {{--            <a href="{{ route('works.index') }}" class="btn btn-m btn-secondary">Մաքրել</a>--}}
            {{--        </form>--}}
{{--        </div>--}}
        <table class="ms-table">
            <thead>
            <tr>
                <td>
                    <a href="{{ route('works.create') }}" class="btn btn-m btn-success">
                        <i class="fa-solid fa-plus"></i> Ստեղծել նորը
                    </a>
                </td>
                <form method="GET" style="display: flex; gap: 5px;">
                    <input type="hidden" name="table" value="active">
                    <td>
                        <input type="text" name="old_serial" placeholder="Հին համար" value="{{ request('old_serial') }}"
                               style="padding: 5px;">
                    </td>
                    <td>

                        <input type="text" name="new_serial" placeholder="Նոր համար" value="{{ request('new_serial') }}"
                               style="padding: 5px;">
                    </td>
                    <td>

                        <button type="submit" class="btn btn-m btn-info">Որոնել</button>
                        <a href="{{ route('works.index', ['table' => 'active']) }}" class="btn btn-m btn-secondary">Մաքրել</a>
                    </td>
                    <td></td>
                </form>
            </tr>
            <tr>
                <th>Մուտքի ամսաթիվ</th>
                {{--                <th>Exit Date</th>--}}
                <th>Հին համար</th>
                <th>Նոր համար</th>
                <th>ԳԳՄ</th>
{{--                <th>ՏՏ</th>--}}
                <th>Սարք</th>
                {{--                <th>Group</th>--}}
                {{--                <th>Group Total Price</th>--}}
                {{--                <th>Representative</th>--}}
                {{--                <th>Non Repairable</th>--}}
                {{--                <th>Conclusion #</th>--}}
                {{--                <th>Status</th>--}}
                {{--                <th>Actions</th>--}}
            </tr>
            </thead>
            <tbody>
            @if(isset($activeWorks))
                @foreach($activeWorks as $work)
                    <tr>
                        <td>
                            <a href="{{ route('works.edit', $work) }}">{{ $work->receive_date ? $work->receive_date->format('Y-m-d') : '-' }}</a>
                        </td>
                        {{--                        <td>{{ $work->exit_date ? $work->exit_date->format('Y-m-d') : '-' }}</td>--}}
                        <td>{{ $work->old_serial_number ?? '-' }}</td>
                        <td>{{ $work->new_serial_number ?? '-' }}</td>
                        <td>{{ $work->partner->region ?? '-' }}</td>
{{--                        <td>{{ $work->partnerStructure->name ?? '-' }}</td>--}}
                        <td>{{ $work->equipment->name ?? '-' }}</td>
                        {{--                        <td>{{ $work->equipmentPartGroup->name ?? '-' }}</td>--}}
                        {{--                        <td>{{ $work->equipment_part_group_total_price ?? '-' }}</td>--}}
                        {{--                        <td>{{ $work->partner_representative ?? '-' }}</td>--}}
                        {{--                        <td>{{ $work->non_repairable ? 'Yes' : 'No' }}</td>--}}
                        {{--                        <td>{{ $work->conclusion_number ?? '-' }}</td>--}}
                        {{--                        <td>{{ $work->status ? 'արխիվ' : 'ընթացիկ' }}</td>--}}
                        {{--                        <td>--}}
                        {{--                            <a href="{{ route('works.edit', $work) }}" class="text-blue-600 hover:underline">Edit</a>--}}
                        {{--                            <form action="{{ route('works.destroy', $work) }}" method="POST" onsubmit="return confirm('Are you sure?');" style="display:inline;">--}}
                        {{--                                @csrf--}}
                        {{--                                @method('DELETE')--}}
                        {{--                                <button type="submit" class="text-red-600 hover:underline">Delete</button>--}}
                        {{--                            </form>--}}
                        {{--                        </td>--}}
                    </tr>
                @endforeach
            @endif
            </tbody>
        </table>
        @if(isset($activeWorks))
            {{ $activeWorks->appends(request()->query())->links('custom-pagination') }}
        @endif
    </div>

    <!-- Archived Works Table -->
    <div id="archived-table" class="data-table-wrapper" style="display: none;">
        <h3>Արխիվ աշխատանքներ (Archived Works)</h3>
{{--        <div style="margin-bottom: 10px;">--}}
{{--            <form method="GET" style="display: flex; gap: 5px;">--}}
{{--                <input type="hidden" name="table" value="archived">--}}
{{--                <input type="text" name="old_serial" placeholder="հին համար" value="{{ request('old_serial') }}"--}}
{{--                       style="padding: 5px;">--}}
{{--                <input type="text" name="new_serial" placeholder="Նոր համար" value="{{ request('new_serial') }}"--}}
{{--                       style="padding: 5px;">--}}
{{--                <button type="submit" class="btn btn-m btn-info">Որոնել</button>--}}
{{--                <a href="{{ route('works.index') }}" class="btn btn-m btn-secondary">Մաքրել</a>--}}
{{--            </form>--}}
{{--        </div>--}}
        <table class="ms-table">
            <thead>
            <tr>
                <td></td>
                <form method="GET" style="display: flex; gap: 5px;">
                    <input type="hidden" name="table" value="archived">
                    <td>
                        <input type="text" name="old_serial" placeholder="Հին համար" value="{{ request('old_serial') }}"
                               style="padding: 5px;">
                    </td>
                    <td>

                        <input type="text" name="new_serial" placeholder="Նոր համար" value="{{ request('new_serial') }}"
                               style="padding: 5px;">
                    </td>
                    <td colspan="5">

                        <button type="submit" class="btn btn-m btn-info">Որոնել</button>
                        <a href="{{ route('works.index', ['table' => 'archived']) }}" class="btn btn-m btn-secondary">Մաքրել</a>
                    </td>
                    <td></td>
                    <td></td>
                    <td></td>
                </form>
            </tr>
            <tr>
                <th>Մուտքի ամսաթիվ</th>
                <th>Ելքի ամսաթիվ</th>
                <th>Հին համար</th>
                <th>Նոր համար</th>
                <th>ԳԳՄ</th>
                <th>ՏՏ</th>
                <th>Սարք</th>
                <th>Կարգ</th>
                <th>Կարգի Գումար</th>
                <th>Չվերանորոգվող</th>
                <th>Կատարողական</th>
            </tr>
            </thead>
            <tbody>
            @if(isset($archivedWorks))
                @foreach($archivedWorks as $work)
                    <tr>
                        <td>
                            <a href="{{ route('works.edit', $work) }}">{{ $work->receive_date ? $work->receive_date->format('Y-m-d') : '-' }}</a>
                        </td>
                        <td>{{ $work->exit_date ? $work->exit_date->format('Y-m-d') : '-' }}</td>
                        <td>{{ $work->old_serial_number ?? '-' }}</td>
                        <td>{{ $work->new_serial_number ?? '-' }}</td>
                        <td>{{ $work->partner->region ?? '-' }}</td>
                        <td>{{ $work->partnerStructure->name ?? '-' }}</td>
                        <td>{{ $work->equipment->name ?? '-' }}</td>
                        <td>{{ $work->equipmentPartGroup->name ?? '-' }}</td>
                        <td>{{ $work->equipment_part_group_total_price ?? '-' }}</td>
                        <td>{{ $work->non_repairable ? 'Այո' : 'Ոչ' }}</td>
                        <td>{{ $work->work_order_status ? 'կատ.' : 'չկա կատ.' }}</td>
                        {{--                        <td>--}}
                        {{--                            <a href="{{ route('works.edit', $work) }}" class="text-blue-600 hover:underline">Edit</a>--}}
                        {{--                            <form action="{{ route('works.destroy', $work) }}" method="POST" onsubmit="return confirm('Are you sure?');" style="display:inline;">--}}
                        {{--                                @csrf--}}
                        {{--                                @method('DELETE')--}}
                        {{--                                <button type="submit" class="text-red-600 hover:underline">Delete</button>--}}
                        {{--                            </form>--}}
                        {{--                        </td>--}}
                    </tr>
                @endforeach
            @endif
            </tbody>
        </table>
        @if(isset($archivedWorks))
            {{ $archivedWorks->appends(request()->query())->links('custom-pagination') }}
        @endif
    </div>

    <script>
        function showTable(type) {
            const activeTable = document.getElementById('active-table');
            const archivedTable = document.getElementById('archived-table');
            const activeBtn = document.getElementById('active-btn');
            const archivedBtn = document.getElementById('archived-btn');

            if (type === 'active') {
                activeTable.style.display = 'block';
                archivedTable.style.display = 'none';
                activeBtn.className = 'btn btn-m btn-primary';
                archivedBtn.className = 'btn btn-m btn-secondary';
            } else {
                activeTable.style.display = 'none';
                archivedTable.style.display = 'block';
                activeBtn.className = 'btn btn-m btn-secondary';
                archivedBtn.className = 'btn btn-m btn-primary';
            }
        }

        // Initialize correct table on page load
        document.addEventListener('DOMContentLoaded', function() {
            const currentTable = '{{ $currentTable }}';
            showTable(currentTable);
        });
    </script>
@endsection
