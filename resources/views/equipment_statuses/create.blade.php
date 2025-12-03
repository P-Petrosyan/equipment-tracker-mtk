@extends('layouts.app')

@section('title', 'Add Status')

@section('content')
    <div class="header">
        <h1 class="page-title">Add Status</h1>
        <a href="{{ route('equipment-statuses.index') }}" class="btn btn-secondary">
            <i class="fa-solid fa-arrow-left"></i> Back
        </a>
    </div>

    <div class="card" style="max-width: 600px;">
        <form id="status-create-form" action="{{ route('equipment-statuses.store') }}" method="POST">
            @csrf

            <div class="form-group">
                <label class="form-label">Status Name *</label>
                <input type="text" name="name" class="form-control" required value="{{ old('name') }}">
                @error('name') <div style="color: red; font-size: 0.875rem; margin-top: 0.25rem;">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-group">
                <label class="form-label">Color *</label>
                <select name="color" class="form-control" required>
                    <option value="gray">Gray</option>
                    <option value="blue">Blue</option>
                    <option value="green">Green</option>
                    <option value="yellow">Yellow</option>
                    <option value="red">Red</option>
                    <option value="indigo">Indigo</option>
                    <option value="purple">Purple</option>
                    <option value="pink">Pink</option>
                </select>
            </div>

            <div class="flex justify-between mt-4">
                <button type="submit" class="btn btn-primary">Save Status</button>
            </div>
        </form>
    </div>

    <script>
        const formId = 'status-create-form';
        const storageKey = 'status_create_draft';

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