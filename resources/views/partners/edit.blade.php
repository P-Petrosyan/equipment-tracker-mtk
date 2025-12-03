@extends('layouts.app')

@section('title', 'Edit Partner')

@section('content')
    <div class="header">
        <h1 class="page-title">Edit Partner: {{ $partner->name }}</h1>
        <a href="{{ route('partners.index') }}" class="btn btn-secondary">
            <i class="fa-solid fa-arrow-left"></i> Back
        </a>
    </div>

    <div class="card" style="max-width: 800px;">
        <form id="partner-edit-form" action="{{ route('partners.update', $partner) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="form-group">
                <label class="form-label">Company Name *</label>
                <input type="text" name="name" class="form-control" required value="{{ old('name', $partner->name) }}">
                @error('name') <div style="color: red; font-size: 0.875rem; margin-top: 0.25rem;">{{ $message }}</div>
                @enderror
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div class="form-group">
                    <label class="form-label">Contact Person</label>
                    <input type="text" name="contact_person" class="form-control"
                        value="{{ old('contact_person', $partner->contact_person) }}">
                </div>
                <div class="form-group">
                    <label class="form-label">Phone</label>
                    <input type="text" name="phone" class="form-control" value="{{ old('phone', $partner->phone) }}">
                </div>
            </div>

            <div class="form-group">
                <label class="form-label">Email</label>
                <input type="email" name="email" class="form-control" value="{{ old('email', $partner->email) }}">
            </div>

            <div class="form-group">
                <label class="form-label">Address</label>
                <textarea name="address" class="form-control">{{ old('address', $partner->address) }}</textarea>
            </div>

            <div class="form-group">
                <label class="form-label">Notes</label>
                <textarea name="notes" class="form-control">{{ old('notes', $partner->notes) }}</textarea>
            </div>

            <div class="flex justify-between mt-4">
                <button type="submit" class="btn btn-primary">Update Partner</button>
            </div>
        </form>
    </div>

    <script>
        const formId = 'partner-edit-form';
        const storageKey = 'partner_edit_draft';

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

        // For edit forms, we might not want to load draft automatically if it overrides DB data inappropriately,
        // but user asked for it. A safer way is to only load if draft exists, but standard behavior is fine.
        document.addEventListener('DOMContentLoaded', loadDraft);
        document.addEventListener('input', saveDraft);
        document.addEventListener('change', saveDraft);

        document.getElementById(formId).addEventListener('submit', () => {
            localStorage.removeItem(storageKey);
        });
    </script>
@endsection