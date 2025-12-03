@extends('layouts.app')

@section('title', 'Add Part')

@section('content')
    <div class="header">
        <h1 class="page-title">Add Part</h1>
        <a href="{{ route('equipment.show', $equipment->id) }}" class="btn btn-secondary">
            <i class="fa-solid fa-arrow-left"></i> Back
        </a>
    </div>

    <div class="card" style="max-width: 800px;">
        <form id="part-create-form" action="{{ route('parts.store') }}" method="POST">
            @csrf
            <input type="hidden" name="equipment_id" value="{{ $equipment->id }}">
            @if($workOrder)
                <input type="hidden" name="work_order_id" value="{{ $workOrder->id }}">
                <div class="alert alert-info mb-4">
                    Adding part to work record: <strong>{{ $workOrder->description }}</strong>
                </div>
            @endif

            <div class="grid grid-cols-2 gap-4">
                <div class="form-group">
                    <label class="form-label">Part Name *</label>
                    <input type="text" name="name" class="form-control" required value="{{ old('name') }}">
                </div>
                <div class="form-group">
                    <label class="form-label">Part Code</label>
                    <input type="text" name="code" class="form-control" value="{{ old('code') }}">
                </div>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div class="form-group">
                    <label class="form-label">Quantity *</label>
                    <input type="number" name="quantity" class="form-control" required min="1"
                        value="{{ old('quantity', 1) }}">
                </div>
                <div class="form-group">
                    <label class="form-label">Unit Price *</label>
                    <input type="number" step="0.01" name="unit_price" class="form-control" required min="0"
                        value="{{ old('unit_price', 0) }}">
                </div>
            </div>

            <div class="flex justify-between mt-4">
                <button type="submit" class="btn btn-primary">Save Part</button>
            </div>
        </form>
    </div>

    <script>
        const formId = 'part-create-form';
        const storageKey = 'part_create_draft';

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