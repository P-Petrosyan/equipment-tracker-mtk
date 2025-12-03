@extends('layouts.app')

@section('title', 'Reports')

@section('content')
    <div class="header">
        <h1 class="page-title">Generate Reports</h1>
    </div>

    <div class="card" style="max-width: 600px;">
        <form id="report-form" action="{{ route('reports.generate') }}" method="POST">
            @csrf

            <div class="form-group">
                <label class="form-label">Partner *</label>
                <select name="partner_id" class="form-control" required>
                    <option value="">Select Partner</option>
                    @foreach($partners as $partner)
                        <option value="{{ $partner->id }}">{{ $partner->name }}</option>
                    @endforeach
                </select>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div class="form-group">
                    <label class="form-label">Start Date *</label>
                    <input type="date" name="start_date" class="form-control" required value="{{ date('Y-m-01') }}">
                </div>
                <div class="form-group">
                    <label class="form-label">End Date *</label>
                    <input type="date" name="end_date" class="form-control" required value="{{ date('Y-m-t') }}">
                </div>
            </div>

            <div class="form-group">
                <label class="form-label">Filter By *</label>
                <select name="date_type" class="form-control" required>
                    <option value="received_at">Equipment Received Date</option>
                    <option value="work_completed">Work Completion Date</option>
                </select>
            </div>

            <div class="form-group">
                <label class="form-label">Format *</label>
                <select name="format" class="form-control" required>
                    <option value="pdf">PDF</option>
                    <option value="excel">Excel</option>
                </select>
            </div>

            <div class="mt-4">
                <button type="submit" class="btn btn-primary">Generate Report</button>
            </div>
        </form>
    </div>

    <script>
        const formId = 'report-form';
        const storageKey = 'report_generate_draft';

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

        // Clear all form drafts when visiting the dashboard
        const draftKeys = [
            'partner_create_draft', 'partner_edit_draft',
            'equipment_create_draft', 'equipment_edit_draft',
            'work_order_create_draft', 'work_order_edit_draft',
            'part_create_draft', 'part_edit_draft',
            'status_create_draft', 'status_edit_draft',
        ];

        draftKeys.forEach(key => localStorage.removeItem(key));
    </script>
@endsection