@extends('layouts.app')

@section('title', 'Create Work')

@section('content')
<div class="header mb-2">
    <h1 class="page-title" style="font-size: 1.2rem;">Ստեղծել նորը</h1>
    <a href="{{ route('works.index') }}" class="btn btn-m btn-secondary">
        <i class="fa-solid fa-arrow-left"></i> Նախորդ էջ
    </a>
</div>

<div class="container">
    @if($errors->any())
        <div style="background-color: #fee2e2; border: 1px solid #fca5a5; color: #991b1b; padding: 1rem; border-radius: 0.5rem; margin-bottom: 1rem;">
            @foreach($errors->all() as $error)
                <p style="margin: 0;">{{ $error }}</p>
            @endforeach
        </div>
    @endif
    
    <form action="{{ route('works.store') }}" method="POST">
        @csrf

        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; max-width: 800px;">
            <div>
                <label>Մուտքի ամսաթիվ:</label>
                <input type="date" name="receive_date" class="form-control" value="{{ date('Y-m-d') }}" required>
            </div>

            <div>
                <label>Ելքի ամսաթիվ:</label>
                <input type="date" name="exit_date" class="form-control">
            </div>

            <div>
                <label>Հին համար:</label>
                <input type="text" name="old_serial_number" id="old-serial" class="form-control" oninput="updateNewSerial()">
            </div>

            <div>
                <label>Նոր համար:</label>
                <input type="text" name="new_serial_number" id="new-serial" class="form-control">
            </div>

            <div>
                <label>Սարք:</label>
                <select name="equipment_id" id="equipment-select" class="form-control" required onchange="updateEquipmentGroups()">
                    @foreach($equipment as $equip)
                        <option value="{{ $equip->id }}">{{ $equip->name }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label>ԳԳՄ:</label>
                <select name="partner_id" id="partner-select" class="form-control" required onchange="updatePartnerStructures()">
                    <option value="">Ընտրել ԳԳՄ</option>
                    @foreach($partners as $partner)
                        <option value="{{ $partner->id }}">{{ $partner->region }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label>Կարգ:</label>
                <select name="equipment_part_group_id" id="group-select" class="form-control" onchange="updateGroupPrice()">
                    <option value="">Ընտրել կարգ</option>
                </select>
            </div>

            <div>
                <label>ՏՏ:</label>
                <select name="partner_structure_id" id="structure-select" class="form-control">
                    <option value="">Ընտրել ՏՏ</option>
                </select>
            </div>

            <div>
                <label>Կարգի գին:</label>
                <input type="number" step="0.01" name="equipment_part_group_total_price" id="group-price" class="form-control" readonly>
            </div>

            <div>
                <label>ԳԳՄ ներկայացուցիչ:</label>
                <input type="text" name="partner_representative" class="form-control">
            </div>

            <div>
                <label>Չվերանորոգվող:</label>
                <input type="hidden" name="non_repairable" value="0">
                <input type="checkbox" name="non_repairable" value="1" onchange="updateConclusionNumber()">

                <div id="print-preview-btn" style="display: none;">
                    <button type="button" onclick="openPrintPreview()" class="btn btn-sm btn-info">
                        <i class="fa-solid fa-print"></i> Տպել
                    </button>
                </div>
            </div>

            <div>
                <label>Եզրակացության համար:</label>
                <input type="text" name="conclusion_number" id="conclusion-number" class="form-control">
            </div>

            <div>
                <label>Թերություն:</label>
                <select id="defect-select" class="form-control" onchange="updateDefectDescription()">
                    <option value="">Ընտրել թերություն</option>
                    @foreach($defects as $defect)
                        <option value="{{ $defect->id }}" data-description="{{ $defect->description }}">{{ $defect->group_id }} {{ $defect->description }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label>Թերության նկարագիր:</label>
                <textarea name="defects_description" id="defects-description" class="form-control" rows="3"></textarea>
            </div>

            <div>
                <label>Կարգավիճակ:</label>
                <select name="status" id="status-select" class="form-control" onchange="toggleWorkOrderStatus()">
                    <option value="0" selected>Ընթացիկ</option>
                    <option value="1">Արխիվ</option>
                </select>
            </div>

{{--            <div id="work-order-status-div" style="display: none;">--}}
{{--                <label>Work Order Status:</label>--}}
{{--                <select name="work_order_status" class="form-control">--}}
{{--                    <option value="0">չկա կատարողական</option>--}}
{{--                    <option value="1">կատարողականով</option>--}}
{{--                </select>--}}
{{--            </div>--}}

        </div>

        <div style="margin-top: 30px;">
            <button type="submit" class="btn btn-primary">Ստղծել</button>
            <a href="{{ route('works.index') }}" class="btn btn-danger">Չեղարկել</a>
        </div>
    </form>
</div>

<script>
    const partnersData = @json($partners);
    const equipmentData = @json($equipment);

    function updatePartnerStructures() {
        const partnerSelect = document.getElementById('partner-select');
        const structureSelect = document.getElementById('structure-select');
        const partnerId = partnerSelect.value;

        structureSelect.innerHTML = '<option value="">Ընտրել ՏՏ</option>';

        if (partnerId) {
            const partner = partnersData.find(p => p.id == partnerId);
            if (partner && partner.structures) {
                partner.structures.forEach(structure => {
                    const option = document.createElement('option');
                    option.value = structure.id;
                    option.textContent = structure.name;
                    structureSelect.appendChild(option);
                });
            }
        }
    }
    updateEquipmentGroups()
    function updateEquipmentGroups() {
        const equipmentSelect = document.getElementById('equipment-select');
        const groupSelect = document.getElementById('group-select');
        const equipmentId = equipmentSelect.value;

        groupSelect.innerHTML = '<option value="">Ընտրել կարգ</option>';

        if (equipmentId) {
            const equipment = equipmentData.find(e => e.id == equipmentId);
            if (equipment && equipment.part_groups) {
                equipment.part_groups.forEach(group => {
                    const option = document.createElement('option');
                    option.value = group.id;
                    option.textContent = group.name + ' - ' + group.notes;
                    option.dataset.totalPrice = group.total_price || 0;
                    groupSelect.appendChild(option);
                });
            }
        }
    }

    function updateNewSerial() {
        const oldSerial = document.getElementById('old-serial').value;
        const newSerial = document.getElementById('new-serial');

        if (oldSerial && !newSerial.dataset.userModified) {
            newSerial.value = oldSerial;
        }
    }

    // Track if user manually modified new serial
    document.addEventListener('DOMContentLoaded', function() {
        document.getElementById('new-serial').addEventListener('input', function() {
            this.dataset.userModified = 'true';
        });
    });

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

    function updateGroupPrice() {
        const groupSelect = document.getElementById('group-select');
        const priceInput = document.getElementById('group-price');
        const selectedOption = groupSelect.options[groupSelect.selectedIndex];

        if (selectedOption && selectedOption.value === '') {
            priceInput.value = '';
        } else if (selectedOption && selectedOption.dataset.totalPrice) {
            priceInput.value = selectedOption.dataset.totalPrice;
        }
    }

    function updateConclusionNumber() {
        const checkbox = document.querySelector('input[name="non_repairable"][type="checkbox"]');
        const printBtn = document.getElementById('print-preview-btn');
        const conclusionInput = document.getElementById('conclusion-number');

        if (checkbox.checked) {
            printBtn.style.display = '';
            if (!conclusionInput.value) {
                conclusionInput.value = {{ $nextConclusionNumber ?? 1 }};
            }
        } else {
            printBtn.style.display = 'none';
            conclusionInput.value = '';
        }
    }

    function openPrintPreview() {
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = '{{ route("works.preview-draft") }}';
        form.target = '_blank';

        const csrfToken = document.createElement('input');
        csrfToken.type = 'hidden';
        csrfToken.name = '_token';
        csrfToken.value = '{{ csrf_token() }}';
        form.appendChild(csrfToken);

        const conclusionInput = document.createElement('input');
        conclusionInput.type = 'hidden';
        conclusionInput.name = 'conclusion_number';
        conclusionInput.value = document.querySelector('[name="conclusion_number"]').value;
        form.appendChild(conclusionInput);

        const serialInput = document.createElement('input');
        serialInput.type = 'hidden';
        serialInput.name = 'old_serial_number';
        serialInput.value = document.querySelector('[name="old_serial_number"]').value;
        form.appendChild(serialInput);

        const equipmentInput = document.createElement('input');
        equipmentInput.type = 'hidden';
        equipmentInput.name = 'equipment_id';
        equipmentInput.value = document.querySelector('[name="equipment_id"]').value;
        form.appendChild(equipmentInput);

        document.body.appendChild(form);
        form.submit();
        document.body.removeChild(form);
    }
</script>
@endsection
