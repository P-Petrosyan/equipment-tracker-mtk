@extends('layouts.app')

@section('title', 'Ընդհանուր տվյալներ')

@section('content')
    <style>
        .ms-access-container {
            display: flex;
            flex-direction: column;
            height: calc(100vh - 100px);
            background-color: #f0f0f0;
            border: 1px solid #ccc;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            font-size: 13px;
        }

        .top-nav-tabs {
            display: flex;
            background: #e1e1e1;
            border-bottom: 1px solid #999;
            padding: 2px 5px;
            overflow-x: auto;
        }

        .nav-tab {
            padding: 4px 12px;
            margin-right: 2px;
            background: #f0f0f0;
            border: 1px solid #999;
            border-radius: 4px;
            cursor: pointer;
            color: #333;
            text-decoration: none;
            white-space: nowrap;
        }

        .nav-tab.active {
            background: #fff;
            font-weight: bold;
            position: relative;
            top: 1px;
        }

        .data-view-container {
            flex: 1;
            padding: 10px;
            background: #fff;
            overflow: auto;
            display: flex;
            flex-direction: column;
            gap: 10px;
            /*white-space: nowrap;*/
        }

        .data-table-wrapper {
            border: 1px solid #999;
            background: #fff;
            overflow: auto;
            position: relative;
            /*max-height: 45%;*/
        }

        .ms-table {
            min-width: 300px;
            border-collapse: collapse;
            font-size: 12px;
        }

        .ms-table th {
            background: #e1e1e1;
            border: 1px solid #999;
            padding: 4px 8px;
            text-align: left;
            font-weight: normal;
            position: sticky;
            top: 0;
            z-index: 10;
        }

        .ms-table td {
            border: 1px solid #ccc;
            padding: 4px 8px;
        }

        .ms-table tr.selected {
            background-color: #cce8ff;
        }

        .ms-table tr:hover {
            background-color: #f0f0f0;
            cursor: pointer;
        }

        .section-header {
            background: #d4d4d4;
            padding: 2px 5px;
            font-weight: bold;
            border: 1px solid #999;
            margin-bottom: 0;
            font-size: 11px;
        }
    </style>

    <div class="header mb-2">
        <h1 class="page-title" style="font-size: 1.2rem;">Ընդհանուր տվյալներ</h1>
        <a href="{{ route('dashboard') }}" class="btn btn-sm btn-secondary">
            <i class="fa-solid fa-arrow-left"></i> Գլխավոր էջ
        </a>
    </div>

    <div class="ms-access-container">
        <div class="top-nav-tabs">
            <a href="{{ route('general-data', 'partners') }}" class="nav-tab {{ $tab === 'partners' ? 'active' : '' }}">ԳԳՄ-ներ</a>
            <a href="{{ route('general-data', 'counters') }}" class="nav-tab {{ $tab === 'counters' ? 'active' : '' }}">Սարքեր/Կարգեր</a>
            <a href="{{ route('general-data', 'reasons') }}" class="nav-tab {{ $tab === 'reasons' ? 'active' : '' }}">Եզրակաց. պատճառները</a>
            <a href="{{ route('general-data', 'defects') }}" class="nav-tab {{ $tab === 'defects' ? 'active' : '' }}">Թերություններ</a>
            <a href="{{ route('general-data', 'positions') }}" class="nav-tab {{ $tab === 'positions' ? 'active' : '' }}">Պաշտոններ</a>
{{--            <a href="{{ route('general-data', 'calendar') }}" class="nav-tab {{ $tab === 'calendar' ? 'active' : '' }}">Օրացույց</a>--}}
            <a href="{{ route('general-data', 'namings') }}" class="nav-tab {{ $tab === 'namings' ? 'active' : '' }}">Անվանումներ</a>
            <a href="{{ route('general-data', 'parts') }}" class="nav-tab {{ $tab === 'parts' ? 'active' : '' }}">Դետալներ</a>
        </div>

        <div class="data-view-container">
            @if($tab === 'partners')
                @include('general_data.partners')
            @elseif($tab === 'counters')
                @include('general_data.counters')
            @elseif($tab === 'reasons')
                @include('general_data.reasons')
            @elseif($tab === 'defects')
                @include('general_data.defects')
            @elseif($tab === 'positions')
                @include('general_data.positions')
            @elseif($tab === 'calendar')
                @include('general_data.calendar')
            @elseif($tab === 'namings')
                @include('general_data.namings')
            @elseif($tab === 'parts')
                @include('general_data.parts')
            @endif
        </div>
    </div>
@endsection
