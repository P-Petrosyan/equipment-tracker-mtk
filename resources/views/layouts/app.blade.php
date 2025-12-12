<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Equipment Repair Tracker')</title>
    <link rel="icon" type="image/webp" href="{{ asset('images/logo.webp') }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
    <!-- Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>

<body>
        <div class="brand">
            <a href="{{ route('dashboard') }}" style="display: flex; align-items: center; gap: 10px;  color: #18326c">
                <img src="{{ asset('images/logo.webp') }}" alt="MTK Logo" style="height: 60px; width: auto;">
                Ձայնաազդանշանային սարքերի վերանորոգում
            </a>
        </div>
{{--    <aside class="sidebar">--}}

{{--        <ul class="nav-links">--}}
{{--            <li>--}}
{{--                <a href="{{ route('dashboard') }}"--}}
{{--                    class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">--}}
{{--                    <i class="fa-solid fa-chart-line"></i> Dashboard--}}
{{--                </a>--}}
{{--            </li>--}}
{{--            <li>--}}
{{--                <a href="{{ route('partners.index') }}"--}}
{{--                    class="nav-link {{ request()->routeIs('partners.*') ? 'active' : '' }}">--}}
{{--                    <i class="fa-solid fa-handshake"></i> Partners--}}
{{--                </a>--}}
{{--            </li>--}}
{{--            <li>--}}
{{--                <a href="{{ route('equipment.index') }}"--}}
{{--                    class="nav-link {{ request()->routeIs('equipment.*') ? 'active' : '' }}">--}}
{{--                    <i class="fa-solid fa-laptop-medical"></i> Equipment--}}
{{--                </a>--}}
{{--            </li>--}}
{{--            <li>--}}
{{--                <a href="{{ route('parts.index') }}"--}}
{{--                    class="nav-link {{ request()->routeIs('parts.*') ? 'active' : '' }}">--}}
{{--                    <i class="fa-solid fa-screwdriver"></i> Parts--}}
{{--                </a>--}}
{{--            </li>--}}
{{--            <li>--}}
{{--                <a href="{{ route('equipment-statuses.index') }}"--}}
{{--                    class="nav-link {{ request()->routeIs('equipment-statuses.*') ? 'active' : '' }}">--}}
{{--                    <i class="fa-solid fa-tags"></i> Statuses--}}
{{--                </a>--}}
{{--            </li>--}}
{{--            <li>--}}
{{--                <a href="{{ route('reports.index') }}"--}}
{{--                    class="nav-link {{ request()->routeIs('reports.*') ? 'active' : '' }}">--}}
{{--                    <i class="fa-solid fa-file-pdf"></i> Reports--}}
{{--                </a>--}}
{{--            </li>--}}
{{--        </ul>--}}
{{--    </aside>--}}

    <main class="main-content">
        @if(session('success'))
            <div class="card" style="background-color: #dcfce7; color: #166534; border: 1px solid #bbf7d0; padding: 0.25rem;">
                <i class="fa-solid fa-check-circle"></i> {{ session('success') }}
            </div>
        @endif

        @yield('content')
    </main>
</body>

</html>
