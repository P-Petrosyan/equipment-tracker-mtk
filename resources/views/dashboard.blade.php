@extends('layouts.app')

@section('title', 'Գլխավոր էջ')

@section('content')
    <style>
        .dashboard-container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 2rem;
        }

        .dashboard-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 4rem;
            /* Large gap between columns */
        }

        .dashboard-column {
            display: flex;
            flex-direction: column;
            gap: 1.5rem;
        }

        .dashboard-btn {
            display: flex;
            align-items: center;
            justify-content: center;
            text-align: center;
            background: #f8f9fa;
            /* Light gray/white */
            padding: 1.5rem 1rem;
            border: 1px solid #e5e7eb;
            border-radius: 4px;
            font-size: 1.2rem;
            font-weight: 600;
            color: #333;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
            transition: all 0.2s ease;
            text-decoration: none;
            min-height: 80px;
        }

        .dashboard-btn:hover {
            background: #fff;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            transform: translateY(-1px);
            border-color: #d1d5db;
        }
    </style>

    <div class="dashboard-container">
        <div class="dashboard-grid">
            <!-- Left Column -->
            <div class="dashboard-column">
                <a href="{{ route('works.index') }}" class="dashboard-btn">
                    Բազա
                </a>
{{--                <a href="{{ route('equipment.index') }}" class="dashboard-btn">--}}
{{--                    Ստուգաչափում<br>Սովորական--}}
{{--                </a>--}}
{{--                <a href="{{ route('equipment.index') }}" class="dashboard-btn">--}}
{{--                    Ստուգաչափում<br>Elcor--}}
{{--                </a>--}}
            </div>

            <!-- Right Column -->
            <div class="dashboard-column">
                <a href="{{ route('general-data') }}" class="dashboard-btn">
                    Ընդհանուր տվյալներ
                </a>
                <a href="{{ route('reference.index') }}" class="dashboard-btn">
                    Հաշվետվություններ
                </a>
                <a href="{{ route('acts.index') }}" class="dashboard-btn">
                    Կատարողական<br>ակտեր
                </a>
                <a href="{{ route('parts.index') }}" class="dashboard-btn">
                    Դետալների մուտք
                </a>
            </div>
        </div>
    </div>

    <script>
        // Clear all form drafts when visiting the dashboard
        const draftKeys = [
            'partner_create_draft', 'partner_edit_draft',
            'equipment_create_draft', 'equipment_edit_draft',
            'work_order_create_draft', 'work_order_edit_draft',
            'part_create_draft', 'part_edit_draft',
            'status_create_draft', 'status_edit_draft',
            'report_generate_draft'
        ];

        draftKeys.forEach(key => localStorage.removeItem(key));
    </script>
@endsection
