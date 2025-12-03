@extends('layouts.app')

@section('title', 'Add Equipment')

@section('content')
    <div class="header">
        <h1 class="page-title">Add Equipment</h1>
        <a href="{{ route('equipment.index') }}" class="btn btn-secondary">
            <i class="fa-solid fa-arrow-left"></i> Back
        </a>
    </div>

    <div class="card" style="max-width: 800px;">
        <form id="equipment-create-form" action="{{ route('equipment.store') }}" method="POST">
            @csrf

            <div class="grid grid-cols-2 gap-4">
                <div class="form-group">
                    <label class="form-label">Partner *</label>
                    <select name="partner_id" class="form-control" required>
                        <option value="">Select Partner</option>
                        @foreach($partners as $partner)
                            <option value="{{ $partner->id }}" {{ (old('partner_id') == $partner->id || (isset($selectedPartnerId) && $selectedPartnerId == $partner->id)) ? 'selected' : '' }}>
                                {{ $partner->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group">
                    <label class="form-label">Status</label>
                    <select name="status_id" class="form-control">
                        @foreach($statuses as $status)
                            <option value="{{ $status->id }}" {{ old('status_id') == $status->id ? 'selected' : '' }}>
                                {{ $status->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div class="form-group">
                    <label class="form-label">Internal ID / Code</label>
                    <input type="text" name="internal_id" class="form-control" value="{{ old('internal_id') }}">
                </div>
                <div class="form-group">
                    <label class="form-label">Serial Number</label>
                    <input type="text" name="serial_number" class="form-control" value="{{ old('serial_number') }}">
                </div>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div class="form-group">
                    <label class="form-label">Model</label>
                    <input type="text" name="model" class="form-control" value="{{ old('model') }}">
                </div>
                <div class="form-group">
                    <label class="form-label">Type / Category</label>
                    <input type="text" name="type" class="form-control" value="{{ old('type') }}">
                </div>
            </div>

            <div class="form-group">
                <label class="form-label">Date Received</label>
                <input type="date" name="received_at" class="form-control" value="{{ old('received_at', date('Y-m-d')) }}">
            </div>

            <div class="form-group">
                <label class="form-label">Problem Description</label>
                <textarea name="description" class="form-control" rows="3">{{ old('description') }}</textarea>
            </div>

            <div class="form-group">
                <label class="form-label">Internal Notes</label>
                <textarea name="notes" class="form-control" rows="2">{{ old('notes') }}</textarea>
            </div>

            <div class="flex justify-between mt-4">
                <button type="submit" class="btn btn-primary">Save Equipment</button>
            </div>
        </form>
    </div>

    <script>
        const formId = 'equipment-create-form';
        const storageKey = 'equipment_create_draft';

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