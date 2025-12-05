@extends('layouts.app')

@section('title', 'Edit Work')

@section('content')
<div class="header mb-2">
    <div style="display: flex; gap: 10px;">

    <h1 class="page-title" style="font-size: 1.2rem;">Edit Work</h1>
    @if($work->old_serial_number)
        <a href="{{ route('works.history', $work->old_serial_number) }}" class="btn btn-sm btn-info">
            <i class="fa-solid fa-history"></i> History
        </a>
    @endif
    </div>

    <a href="{{ route('works.index') }}" class="btn btn-sm btn-secondary">
        <i class="fa-solid fa-arrow-left"></i> Back to Works
    </a>
</div>

<div class="container">
    <form action="{{ route('works.update', $work) }}" method="POST">
        @csrf
        @method('PATCH')

        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; max-width: 800px;">
            <div>
                <label>Receive Date:</label>
                <input type="date" name="receive_date" value="{{ $work->receive_date ? $work->receive_date->format('Y-m-d') : '' }}" class="form-control" required>
            </div>

            <div>
                <label>Exit Date:</label>
                <input type="date" name="exit_date" value="{{ $work->exit_date ? $work->exit_date->format('Y-m-d') : '' }}" class="form-control">
            </div>

            <div>
                <label>Old Serial Number:</label>
                <input type="text" name="old_serial_number" id="old-serial" value="{{ $work->old_serial_number }}" class="form-control" oninput="updateNewSerial()">
            </div>

            <div>
                <label>New Serial Number:</label>
                <input type="text" name="new_serial_number" id="new-serial" value="{{ $work->new_serial_number }}" class="form-control">
            </div>

            <div>
                <label>Equipment:</label>
                <select name="equipment_id" id="equipment-select" class="form-control" required onchange="updateEquipmentGroups()">
                    @foreach($equipment as $equip)
                        <option value="{{ $equip->id }}" {{ $work->equipment_id == $equip->id ? 'selected' : '' }}>
                            {{ $equip->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div>
                <label>Partner:</label>
                <select name="partner_id" id="partner-select" class="form-control" required onchange="updatePartnerStructures()">
                    @foreach($partners as $partner)
                        <option value="{{ $partner->id }}" {{ $work->partner_id == $partner->id ? 'selected' : '' }}>
                            {{ $partner->region }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div>
                <label>Group:</label>
                <select name="equipment_part_group_id" id="group-select" class="form-control" onchange="updateGroupPrice()">
                    <option value="">Select Group</option>
                </select>
            </div>

            <div>
                <label>Structure:</label>
                <select name="partner_structure_id" id="structure-select" class="form-control">
                    <option value="">Select Structure</option>
                </select>
            </div>

            <div>
                <label>Group Total Price:</label>
                <input type="number" step="0.01" name="equipment_part_group_total_price" id="group-price"
                       value="{{ $work->equipment_part_group_total_price }}" class="form-control" readonly>
            </div>

            <div>
                <label>Partner Representative:</label>
                <input type="text" name="partner_representative" value="{{ $work->partner_representative }}" class="form-control">
            </div>

            <div>
                <label>Non Repairable:</label>
                <input type="checkbox" name="non_repairable" value="1" {{ $work->non_repairable ? 'checked' : '' }} onchange="updateConclusionNumber()">

                <div>
                    @if($work->non_repairable && $work->conclusion_number)
                        <a href="{{ route('works.print-preview', $work) }}" target="_blank" class="btn btn-sm btn-info">
                            <i class="fa-solid fa-print"></i> Print Preview
                        </a>
                    @endif
                </div>
            </div>



            <div>
                <label>Conclusion Number:</label>
                <input type="text" name="conclusion_number" id="conclusion-number" value="{{ $work->conclusion_number }}" class="form-control">
            </div>

            <div>
                <label>Defect:</label>
                <select id="defect-select" class="form-control" onchange="updateDefectDescription()">
                    <option value="">Select Defect</option>
                    @foreach($defects as $defect)
                        <option value="{{ $defect->id }}" data-description="{{ $defect->description }}">{{ $defect->group_id }} {{ $defect->description }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label>Defects Description:</label>
                <textarea name="defects_description" id="defects-description" class="form-control" rows="3">{{ $work->defects_description }}</textarea>
            </div>

            <div>
                <label>Status:</label>
                <select name="status" id="status-select" class="form-control" onchange="toggleWorkOrderStatus()">
                    <option value="0" {{ $work->status == 0 ? 'selected' : '' }}>Ընթացիկ</option>
                    <option value="1" {{ $work->status == 1 ? 'selected' : '' }}>Արխիվ</option>
                </select>
            </div>

            <div id="work-order-status-div" style="display: {{ $work->status == 1 ? 'block' : 'none' }};">
                <label>Work Order Status:</label>
                <select name="work_order_status" class="form-control">
                    <option value="0" {{ $work->work_order_status == 0 ? 'selected' : '' }}>Չկա կատարողական</option>
                    <option value="1" {{ $work->work_order_status == 1 ? 'selected' : '' }}>Կատարողականով</option>
                </select>
            </div>

        </div>

        <div style="margin-top: 30px;">
            <button type="submit" class="btn btn-primary">Update Work</button>
            <a href="{{ route('works.index') }}" class="btn btn-secondary">Cancel</a>
        </div>
    </form>
</div>

<script>
    const partnersData = @json($partners);
    const equipmentData = @json($equipment);
    const currentWork = @json($work);

    function updatePartnerStructures() {
        const partnerSelect = document.getElementById('partner-select');
        const structureSelect = document.getElementById('structure-select');
        const partnerId = partnerSelect.value;

        structureSelect.innerHTML = '<option value="">Select Structure</option>';

        if (partnerId) {
            const partner = partnersData.find(p => p.id == partnerId);
            if (partner && partner.structures) {
                partner.structures.forEach(structure => {
                    const option = document.createElement('option');
                    option.value = structure.id;
                    option.textContent = structure.name;
                    if (currentWork.partner_structure_id == structure.id) {
                        option.selected = true;
                    }
                    structureSelect.appendChild(option);
                });
            }
        }
    }

    function updateEquipmentGroups() {
        const equipmentSelect = document.getElementById('equipment-select');
        const groupSelect = document.getElementById('group-select');
        const equipmentId = equipmentSelect.value;

        groupSelect.innerHTML = '<option value="">Select Group</option>';

        if (equipmentId) {
            const equipment = equipmentData.find(e => e.id == equipmentId);
            if (equipment && equipment.part_groups) {
                equipment.part_groups.forEach(group => {
                    const option = document.createElement('option');
                    option.value = group.id;
                    option.textContent = group.name;
                    option.dataset.totalPrice = group.total_price || 0;
                    if (currentWork.equipment_part_group_id == group.id) {
                        option.selected = true;
                    }
                    groupSelect.appendChild(option);
                });
            }
        }
    }

    function updateGroupPrice() {
        const groupSelect = document.getElementById('group-select');
        const priceInput = document.getElementById('group-price');
        const selectedOption = groupSelect.options[groupSelect.selectedIndex];

        if (selectedOption && selectedOption.dataset.totalPrice) {
            priceInput.value = selectedOption.dataset.totalPrice;
        }
    }

    function updateConclusionNumber() {
        const checkbox = document.querySelector('input[name="non_repairable"]');
        const conclusionInput = document.getElementById('conclusion-number');

        if (checkbox.checked) {
            if (!conclusionInput.value) {
                conclusionInput.value = {{ $nextConclusionNumber }};
            }
        } else {
            conclusionInput.value = '';
        }
    }

    function toggleWorkOrderStatus() {
        const statusSelect = document.getElementById('status-select');
        const workOrderDiv = document.getElementById('work-order-status-div');

        if (statusSelect.value === '1') {
            workOrderDiv.style.display = 'block';
        } else {
            workOrderDiv.style.display = 'none';
        }
    }

    function updateNewSerial() {
        const oldSerial = document.getElementById('old-serial').value;
        const newSerial = document.getElementById('new-serial');

        if (oldSerial && !newSerial.dataset.userModified) {
            newSerial.value = oldSerial;
        }
    }

    function updateDefectDescription() {
        const defectSelect = document.getElementById('defect-select');
        const descriptionTextarea = document.getElementById('defects-description');
        const selectedOption = defectSelect.options[defectSelect.selectedIndex];

        if (selectedOption && selectedOption.dataset.description) {
            descriptionTextarea.value = selectedOption.dataset.description;
        } else {
            descriptionTextarea.value = '';
        }
    }

    // Track if user manually modified new serial
    document.getElementById('new-serial').addEventListener('input', function() {
        this.dataset.userModified = 'true';
    });

    // Initialize dropdowns on page load
    document.addEventListener('DOMContentLoaded', function() {
        updatePartnerStructures();
        updateEquipmentGroups();
    });
</script>
@endsection
