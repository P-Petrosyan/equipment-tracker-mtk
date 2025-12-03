@extends('layouts.app')

@section('title', 'Edit Work Record')

@section('content')
    <div class="header">
        <h1 class="page-title">Edit Work Record</h1>
        <a href="{{ route('equipment.show', $workOrder->equipment_id) }}" class="btn btn-secondary">
            <i class="fa-solid fa-arrow-left"></i> Back
        </a>
    </div>

    <div class="card" style="max-width: 800px;">
        <form id="work-order-edit-form" action="{{ route('work-orders.update', $workOrder) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="form-group">
                <label class="form-label">Description *</label>
                <textarea name="description" class="form-control" rows="3"
                    required>{{ old('description', $workOrder->description) }}</textarea>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div class="form-group">
                    <label class="form-label">Technician Name</label>
                    <input type="text" name="technician_name" class="form-control"
                        value="{{ old('technician_name', $workOrder->technician_name) }}">
                </div>
                <div class="form-group">
                    <label class="form-label">Work Type / Category</label>
                    <input type="text" name="type" class="form-control" value="{{ old('type', $workOrder->type) }}">
                </div>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div class="form-group">
                    <label class="form-label">Start Date</label>
                    <input type="date" name="start_date" class="form-control"
                        value="{{ old('start_date', optional($workOrder->start_date)->format('Y-m-d')) }}">
                </div>
                <div class="form-group">
                    <label class="form-label">Completion Date</label>
                    <input type="date" name="end_date" class="form-control"
                        value="{{ old('end_date', optional($workOrder->end_date)->format('Y-m-d')) }}">
                </div>
            </div>

            <div class="form-group">
                <label class="form-label">Labor Cost</label>
                <input type="number" step="0.01" name="labor_cost" class="form-control"
                    value="{{ old('labor_cost', $workOrder->labor_cost) }}">
            </div>

            <div class="flex justify-between mt-4">
                <button type="submit" class="btn btn-primary">Update Work Record</button>
            </div>
        </form>
    </div>

    <script>
        const formId = 'work-order-edit-form';
        const storageKey = 'work_order_edit_draft';

        function loadDraft() {
            const data = localStorage.getItem(storageKey);
            if (!data) return;

            try {
                const values = JSON.parse(data);
                Object.keys(values).forEach(name => {
                    const field = document.querySelector(`[name="${name}"]`);
                    if (field) {
                        field.value = values[name];
                    }
                });
            } catch (e) {
                console.error('Cannot parse draft', e);
            }
        }

        function saveDraft() {
            const form = document.getElementById(formId);
            const data = {};
            Array.from(form.elements).forEach(el => {
                if (el.name && ['INPUT', 'TEXTAREA', 'SELECT'].includes(el.tagName)) {
                    data[el.name] = el.value;
                }
            });
            localStorage.setItem(storageKey, JSON.stringify(data));
        }

        document.addEventListener('DOMContentLoaded', loadDraft);
        document.addEventListener('input', saveDraft);
        document.addEventListener('change', saveDraft);

        document.getElementById(formId).addEventListener('submit', () => {
            localStorage.removeItem(storageKey);
        });
    </script>
@endsection