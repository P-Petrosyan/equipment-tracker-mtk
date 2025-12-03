@extends('layouts.app')

@section('title', 'Edit Status')

@section('content')
    <div class="header">
        <h1 class="page-title">Edit Status</h1>
        <a href="{{ route('equipment-statuses.index') }}" class="btn btn-secondary">
            <i class="fa-solid fa-arrow-left"></i> Back
        </a>
    </div>

    <div class="card" style="max-width: 600px;">
        <form id="status-edit-form" action="{{ route('equipment-statuses.update', $equipmentStatus) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="form-group">
                <label class="form-label">Status Name *</label>
                <input type="text" name="name" class="form-control" required
                    value="{{ old('name', $equipmentStatus->name) }}">
                @error('name') <div style="color: red; font-size: 0.875rem; margin-top: 0.25rem;">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-group">
                <label class="form-label">Color *</label>
                <select name="color" class="form-control" required>
                    @foreach(['gray', 'blue', 'green', 'yellow', 'red', 'indigo', 'purple', 'pink'] as $color)
                        <option value="{{ $color }}" {{ old('color', $equipmentStatus->color) == $color ? 'selected' : '' }}>
                            {{ ucfirst($color) }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="flex justify-between mt-4">
                <button type="submit" class="btn btn-primary">Update Status</button>
            </div>
        </form>
    </div>

    <script>
        const formId = 'status-edit-form';
        const storageKey = 'status_edit_draft';

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