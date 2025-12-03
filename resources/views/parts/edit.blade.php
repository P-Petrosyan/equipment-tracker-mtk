@extends('layouts.app')

@section('title', 'Edit Part')

@section('content')
    <div class="header">
        <h1 class="page-title">Edit Part</h1>
        <a href="{{ route('equipment.show', $part->equipment_id) }}" class="btn btn-secondary">
            <i class="fa-solid fa-arrow-left"></i> Back
        </a>
    </div>

    <div class="card" style="max-width: 800px;">
        <form id="part-edit-form" action="{{ route('parts.update', $part) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="grid grid-cols-2 gap-4">
                <div class="form-group">
                    <label class="form-label">Part Name *</label>
                    <input type="text" name="name" class="form-control" required value="{{ old('name', $part->name) }}">
                </div>
                <div class="form-group">
                    <label class="form-label">Part Code</label>
                    <input type="text" name="code" class="form-control" value="{{ old('code', $part->code) }}">
                </div>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div class="form-group">
                    <label class="form-label">Quantity *</label>
                    <input type="number" name="quantity" class="form-control" required min="1"
                        value="{{ old('quantity', $part->quantity) }}">
                </div>
                <div class="form-group">
                    <label class="form-label">Unit Price *</label>
                    <input type="number" step="0.01" name="unit_price" class="form-control" required min="0"
                        value="{{ old('unit_price', $part->unit_price) }}">
                </div>
            </div>

            <div class="flex justify-between mt-4">
                <button type="submit" class="btn btn-primary">Update Part</button>
            </div>
        </form>
    </div>

    <script>
        const formId = 'part-edit-form';
        const storageKey = 'part_edit_draft';

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