@extends('layouts.app')
@section('title', 'Manajemen Unit')

@section('content')
<div class="unit-management-container">
    
    <div class="page-header">
        <div>
            <h1 class="page-title">Manajemen <span class="text-brand">Unit</span></h1>
            <p class="page-subtitle">Data unit ditarik langsung dari Auth/SSO</p>
        </div>
    </div>

    @if(session('success'))
        <div class="alert-success glass mb-6">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
            </svg>
            {{ session('success') }}
        </div>
    @endif

    <div class="search-section glass">
        <form action="{{ route('units.index') }}" method="GET" class="search-form">
            <input type="hidden" name="per_page" value="{{ request('per_page', 10) }}">
            <div class="search-input-wrapper">
                <svg class="w-5 h-5 search-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                </svg>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari Nama Unit..." class="search-input">
            </div>
            
            <button type="submit" class="btn-search">Cari</button>
            @if(request('search'))
                <a href="{{ route('units.index', ['per_page' => request('per_page', 10)]) }}" class="btn-reset">Reset</a>
            @endif
        </form>
    </div>

    <div class="table-container glass">
        <div class="table-wrapper">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Nama Unit</th>
                        <th>Dibuat</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($units as $index => $unit)
                    <tr>
                        <td>{{ $units->firstItem() + $index }}</td>
                        <td class="font-semibold">{{ $unit->name }}</td>
                        <td>{{ $unit->created_at->isoFormat('D MMM Y') }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="3" class="empty-state">
                            <div class="empty-icon">
                                <svg class="w-16 h-16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                                </svg>
                            </div>
                            <p class="empty-text">Belum ada data unit</p>
                            <p class="empty-subtext">Data unit dari Auth/SSO tidak ditemukan.</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="table-footer glass">
            <div class="per-page-footer">
                <form action="{{ route('units.index') }}" method="GET" id="perPageForm">
                    @foreach(request()->except(['per_page', 'page']) as $key => $value)
                        <input type="hidden" name="{{ $key }}" value="{{ $value }}">
                    @endforeach
                    <label for="per_page">Tampilkan:</label>
                    <select name="per_page" onchange="this.form.submit()" class="footer-select">
                        <option value="10" {{ request('per_page', 10) == 10 ? 'selected' : '' }}>10</option>
                        <option value="25" {{ request('per_page') == 25 ? 'selected' : '' }}>25</option>
                        <option value="50" {{ request('per_page') == 50 ? 'selected' : '' }}>50</option>
                        <option value="100" {{ request('per_page') == 100 ? 'selected' : '' }}>100</option>
                    </select>
                </form>
            </div>
            
            @if($units->hasPages())
            <div class="pagination-wrapper">
                {{ $units->links() }}
            </div>
            @endif
        </div>
    </div>
</div>

@push('scripts')
<style>
    :root {
        --brand-blue: #3b82f6;
        --brand-blue-dark: #2563eb;
        --brand-green: #10b981;
        --brand-red: #ef4444;
        
        /* Light mode */
        --text-main: #1e293b;
        --text-secondary: #64748b;
        --text-dim: #94a3b8;
        --card-bg: #ffffff;
        --card-border: rgba(0, 0, 0, 0.08);
        --glass-bg: rgba(255, 255, 255, 0.8);
        --hover-bg: rgba(0, 0, 0, 0.03);
    }

    .dark {
        /* Dark mode */
        --text-main: #f8fafc;
        --text-secondary: #94a3b8;
        --text-dim: #64748b;
        --card-bg: #1f2937;
        --card-border: rgba(255, 255, 255, 0.08);
        --glass-bg: rgba(31, 41, 55, 0.8);
        --hover-bg: rgba(255, 255, 255, 0.05);
        --shadow-color: rgba(0, 0, 0, 0.3);
    }

    .unit-management-container {
        max-width: 1400px;
        margin: 0 auto;
        padding: 60px 20px 40px;
        font-family: 'Outfit', sans-serif;
    }

    .search-section {
        margin-bottom: 24px;
        padding: 24px;
        border-radius: 20px;
        background: var(--glass-bg);
        border: 1px solid var(--card-border);
        box-shadow: 0 8px 32px var(--shadow-color);
    }

    .search-form {
        display: flex;
        gap: 16px;
        align-items: center;
        flex-wrap: wrap;
    }

    .search-input-wrapper {
        position: relative;
        flex: 1;
        min-width: 300px;
    }

    .search-icon {
        position: absolute;
        left: 16px;
        top: 50%;
        transform: translateY(-50%);
        color: var(--text-dim);
        transition: color 0.3s;
    }

    .search-input {
        width: 100%;
        padding: 14px 14px 14px 48px;
        background: var(--hover-bg);
        border: 1px solid var(--card-border);
        border-radius: 14px;
        color: var(--text-main);
        font-size: 0.95rem;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    }

    .search-input:focus {
        outline: none;
        border-color: var(--brand-blue);
        background: var(--card-bg);
        box-shadow: 0 0 0 4px rgba(59, 130, 246, 0.15);
        transform: translateY(-1px);
    }

    .search-input:focus ~ .search-icon {
        color: var(--brand-blue);
    }

    .btn-search {
        padding: 14px 28px;
        background: var(--brand-blue);
        color: white;
        border: none;
        border-radius: 14px;
        font-weight: 700;
        cursor: pointer;
        transition: all 0.3s;
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .btn-search:hover {
        background: var(--brand-blue-dark);
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(59, 130, 246, 0.3);
    }

    .btn-reset {
        padding: 14px 28px;
        background: var(--hover-bg);
        color: var(--text-secondary);
        border: 1px solid var(--card-border);
        border-radius: 14px;
        font-weight: 700;
        text-decoration: none;
        transition: all 0.3s;
    }

    .btn-reset:hover {
        background: var(--card-bg);
        border-color: var(--brand-red);
        color: var(--brand-red);
        transform: translateY(-2px);
    }

    .table-footer {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 20px;
        border-top: 1px solid var(--card-border);
        flex-wrap: wrap;
        gap: 20px;
    }

    .per-page-footer {
        display: flex;
        align-items: center;
        gap: 10px;
        color: var(--text-secondary);
        font-weight: 600;
        font-size: 0.875rem;
    }

    .per-page-footer form {
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .footer-select {
        padding: 8px 16px;
        background: var(--hover-bg);
        border: 1px solid var(--card-border);
        border-radius: 10px;
        color: var(--text-main);
        font-weight: 600;
        cursor: pointer;
        transition: all 0.3s;
        appearance: none;
        background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 24 24' stroke='%236b7280'%3E%3Cpath stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='M19 9l-7 7-7-7'%3E%3C/path%3E%3C/svg%3E");
        background-repeat: no-repeat;
        background-position: right 12px center;
        background-size: 16px;
        padding-right: 40px;
    }

    .dark .footer-select {
        background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 24 24' stroke='%2394a3b8'%3E%3Cpath stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='M19 9l-7 7-7-7'%3E%3C/path%3E%3C/svg%3E");
    }

    .footer-select:focus {
        outline: none;
        border-color: var(--brand-blue);
        background-color: var(--card-bg);
    }


    .page-header {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        margin-bottom: 40px;
        flex-wrap: wrap;
        gap: 20px;
    }

    .page-title {
        font-size: clamp(2rem, 4vw, 3rem);
        font-weight: 900;
        letter-spacing: -2px;
        color: var(--text-main);
        margin: 0;
    }

    .text-brand { color: var(--brand-blue); }

    .page-subtitle {
        color: var(--text-secondary);
        margin-top: 8px;
        font-size: 1rem;
    }

    .alert-success {
        display: flex;
        align-items: center;
        gap: 12px;
        padding: 16px 20px;
        background: rgba(16, 185, 129, 0.1);
        border: 1px solid rgba(16, 185, 129, 0.3);
        border-radius: 12px;
        color: var(--brand-green);
        margin-bottom: 24px;
        font-weight: 600;
    }

    .table-container {
        background: var(--glass-bg);
        border: 1px solid var(--card-border);
        border-radius: 24px;
        overflow: hidden;
        backdrop-filter: blur(20px);
    }

    .table-wrapper {
        overflow-x: auto;
    }

    .data-table {
        width: 100%;
        border-collapse: collapse;
    }

    .data-table thead {
        background: var(--hover-bg);
        border-bottom: 2px solid var(--card-border);
    }

    .data-table th {
        padding: 16px;
        text-align: left;
        font-size: 0.75rem;
        font-weight: 900;
        color: var(--text-secondary);
        text-transform: uppercase;
        letter-spacing: 1px;
    }

    .data-table td {
        padding: 16px;
        color: var(--text-main);
        border-bottom: 1px solid var(--card-border);
    }

    .data-table tbody tr {
        transition: background 0.2s;
    }

    .data-table tbody tr:hover {
        background: var(--hover-bg);
    }

    .shifts-container, .days-container {
        display: flex;
        flex-wrap: wrap;
        gap: 6px;
    }

    .shift-badge-small {
        display: inline-block;
        padding: 3px 10px;
        background: rgba(16, 185, 129, 0.1);
        color: var(--brand-green);
        border-radius: 6px;
        font-weight: 600;
        font-size: 0.75rem;
    }

    .day-badge {
        display: inline-block;
        padding: 3px 10px;
        background: rgba(59, 130, 246, 0.1);
        color: var(--brand-blue);
        border-radius: 6px;
        font-weight: 600;
        font-size: 0.75rem;
    }

    .text-dim {
        color: var(--text-dim);
        font-style: italic;
    }

    .empty-state {
        text-align: center;
        padding: 60px 20px !important;
    }

    .empty-icon {
        color: var(--text-dim);
        margin-bottom: 16px;
    }

    .empty-text {
        font-size: 1.125rem;
        font-weight: 700;
        color: var(--text-main);
        margin-bottom: 8px;
    }

    .empty-subtext {
        color: var(--text-secondary);
        font-size: 0.875rem;
    }

    /* Pagination Styles */
    .pagination-container {
        margin-top: 20px;
    }

    .glass {
        backdrop-filter: blur(20px);
    }

    @media (max-width: 768px) {
        .unit-management-container {
            padding: 40px 15px 30px;
        }

        .page-header {
            flex-direction: column;
        }

        .data-table {
            font-size: 0.875rem;
        }

        .data-table th,
        .data-table td {
            padding: 12px 8px;
        }
    }
</style>
@endpush
@endsection
