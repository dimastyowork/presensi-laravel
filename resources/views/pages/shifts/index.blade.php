@extends('layouts.app')
@section('title', 'Manajemen Shift')

@section('content')
<div class="shift-management-container">
    
    <!-- Header -->
    <div class="page-header">
        <div>
            <h1 class="page-title">Manajemen <span class="text-brand">Shift</span></h1>
            <p class="page-subtitle">Kelola daftar shift kerja yang tersedia</p>
        </div>
        <a href="{{ route('shifts.create') }}" class="btn-primary">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
            Tambah Shift
        </a>
    </div>

    <!-- Success Message -->
    @if(session('success'))
        <div class="alert-success glass mb-6">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
            </svg>
            {{ session('success') }}
        </div>
    @endif

    <!-- Search Section -->
    <div class="search-section glass">
        <form action="{{ route('shifts.index') }}" method="GET" class="search-form">
            <input type="hidden" name="per_page" value="{{ request('per_page', 10) }}">
            <div class="search-input-wrapper">
                <svg class="w-5 h-5 search-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                </svg>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari Nama Shift..." class="search-input">
            </div>
            
            <button type="submit" class="btn-search">Cari</button>
            @if(request('search'))
                <a href="{{ route('shifts.index', ['per_page' => request('per_page', 10)]) }}" class="btn-reset">Reset</a>
            @endif
        </form>
    </div>

    <!-- Table -->
    <div class="table-container glass">
        <div class="table-wrapper">
            <table class="data-table">
                <thead>
                    <tr>
                        <th style="width: 80px;">Aksi</th>
                        <th>Shift & Hari Kerja</th>
                        <th>Jam Kerja</th>
                        <th>Dibuat</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($shifts as $index => $shift)
                    <tr>
                        {{-- Aksi --}}
                        <td>
                            <div class="action-buttons">
                                <a href="{{ route('shifts.edit', $shift->id) }}" class="btn-action edit" title="Edit">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                    </svg>
                                </a>
                                <form action="{{ route('shifts.destroy', $shift->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus shift ini?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn-action delete" title="Hapus">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                        </svg>
                                    </button>
                                </form>
                            </div>
                        </td>

                        {{-- Shift & Hari --}}
                        <td>
                            <div class="flex flex-col gap-1.5">
                                <span class="font-bold text-main">{{ $shift->name }}</span>
                                <div class="days-container">
                                    @if(is_array($shift->working_days) && count($shift->working_days) > 0)
                                        @foreach($shift->working_days as $day)
                                            <span class="day-badge">{{ $day }}</span>
                                        @endforeach
                                    @else
                                        <span class="text-dim text-[10px]">Semua Hari</span>
                                    @endif
                                </div>
                            </div>
                        </td>

                        {{-- Jam Kerja Stacked --}}
                        <td>
                            <div class="attendance-stack">
                                <div class="stack-item in">
                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M11 16l-4-4m0 0l4-4m-4 4h14" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
                                    <span class="time">{{ \Carbon\Carbon::parse($shift->start_time)->format('H:i') }}</span>
                                </div>
                                <div class="stack-item out">
                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M17 16l4-4m0 0l-4-4m4 4H7" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
                                    <span class="time">{{ \Carbon\Carbon::parse($shift->end_time)->format('H:i') }}</span>
                                </div>
                            </div>
                        </td>

                        {{-- Dibuat --}}
                        <td class="text-sm text-secondary">{{ $shift->created_at->isoFormat('D MMM Y') }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" class="empty-state">
                            <div class="empty-icon">
                                <svg class="w-16 h-16 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                            </div>
                            <p class="empty-text">Tidak ada shift ditemukan</p>
                            <p class="empty-subtext">Mulai dengan menambahkan shift baru menggunakan tombol di atas.</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="table-footer glass">
            <div class="per-page-footer">
                <form action="{{ route('shifts.index') }}" method="GET" id="perPageForm">
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
            @if($shifts->hasPages())
            <div class="pagination-wrapper">
                {{ $shifts->links() }}
            </div>
            @endif
        </div>
    </div>
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const tableWrapper = document.querySelector('.table-wrapper');
        if (tableWrapper) {
            tableWrapper.addEventListener('scroll', function() {
                if (this.scrollLeft > 5) {
                    this.classList.add('is-scrolled');
                } else {
                    this.classList.remove('is-scrolled');
                }
            });
        }
    });
</script>
<style>
    :root {
        --brand-blue: #3b82f6;
        --brand-blue-dark: #2563eb;
        --brand-green: #10b981;
        --brand-red: #ef4444;
        --brand-yellow: #f59e0b;
        --text-main: #1e293b;
        --text-secondary: #64748b;
        --text-dim: #94a3b8;
        --card-bg: #ffffff;
        --card-border: rgba(0, 0, 0, 0.08);
        --glass-bg: rgba(255, 255, 255, 0.8);
        --hover-bg: rgba(0, 0, 0, 0.03);
        --header-bg: #f8fafc;
        --shadow-color: rgba(0, 0, 0, 0.05);
    }

    .dark {
        --text-main: #f8fafc;
        --text-secondary: #94a3b8;
        --text-dim: #64748b;
        --card-bg: #1f2937;
        --card-border: rgba(255, 255, 255, 0.08);
        --glass-bg: rgba(31, 41, 55, 0.8);
        --hover-bg: rgba(255, 255, 255, 0.05);
        --header-bg: #262f3f;
        --shadow-color: rgba(0, 0, 0, 0.3);
    }


    /* Attendance Pill / Stack Styles */
    .attendance-stack {
        display: flex;
        flex-direction: column;
        gap: 3px;
    }
    .stack-item {
        display: flex;
        align-items: center;
        gap: 6px;
        font-size: 0.8rem;
        font-weight: 700;
        padding: 2px 8px;
        border-radius: 6px;
        width: fit-content;
    }
    .stack-item.in {
        background: rgba(37, 99, 235, 0.08);
        color: var(--brand-blue);
    }
    .stack-item.out {
        background: rgba(249, 115, 22, 0.08); 
        color: #f97316;
    }
    .stack-item .time {
        font-family: 'Monaco', 'Consolas', monospace;
    }

    .shift-management-container {
        max-width: 1400px;
        margin: 0 auto;
        padding: 20px 20px 40px;
        font-family: 'Outfit', sans-serif;
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

    .btn-primary {
        display: flex;
        align-items: center;
        gap: 8px;
        padding: 12px 24px;
        background: var(--brand-blue);
        color: white;
        border-radius: 12px;
        font-weight: 700;
        text-decoration: none;
        transition: all 0.3s;
        border: none;
        cursor: pointer;
    }

    .btn-primary:hover {
        background: var(--brand-blue-dark);
        transform: translateY(-2px);
        box-shadow: 0 10px 20px rgba(59, 130, 246, 0.3);
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
        background: var(--header-bg);
        position: sticky;
        top: 0;
        z-index: 20;
    }

    .data-table thead::after {
        content: '';
        position: absolute;
        left: 0;
        right: 0;
        bottom: 0;
        height: 1px;
        background: var(--card-border);
    }

    .data-table th {
        padding: 16px;
        text-align: left;
        font-size: 0.75rem;
        font-weight: 900;
        color: var(--text-secondary);
        text-transform: uppercase;
        letter-spacing: 1px;
        background: inherit;
        position: sticky;
        top: 0;
        z-index: 15;
    }

    /* Row Hover Effect */
    .data-table tbody tr { transition: all 0.2s ease; }
    .data-table tbody tr:hover td { background: var(--hover-bg) !important; }

    .data-table td {
        padding: 16px;
        color: var(--text-main);
        border-bottom: 1px solid var(--card-border);
    }

    .time-badge {
        display: inline-block;
        padding: 4px 12px;
        border-radius: 8px;
        font-weight: 600;
        font-size: 0.875rem;
    }

    .time-badge.in {
        background: rgba(16, 185, 129, 0.1);
        color: var(--brand-green);
    }

    .time-badge.out {
        background: rgba(245, 158, 11, 0.1);
        color: var(--brand-yellow);
    }

    .days-container {
        display: flex;
        flex-wrap: wrap;
        gap: 6px;
    }

    .day-badge {
        display: inline-flex;
        align-items: center;
        padding: 2px 8px;
        border-radius: 999px;
        font-size: 0.75rem;
        font-weight: 600;
        background: rgba(59, 130, 246, 0.1);
        color: var(--brand-blue);
    }

    .text-dim {
        color: var(--text-dim);
        font-size: 0.875rem;
    }

    .action-buttons {
        display: flex;
        gap: 8px;
    }

    .btn-action {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 36px;
        height: 36px;
        border-radius: 8px;
        border: none;
        cursor: pointer;
        transition: all 0.3s;
    }

    .btn-action.edit {
        background: rgba(59, 130, 246, 0.1);
        color: var(--brand-blue);
    }

    .btn-action.edit:hover {
        background: var(--brand-blue);
        color: white;
    }

    .btn-action.delete {
        background: rgba(239, 68, 68, 0.1);
        color: var(--brand-red);
    }

    .btn-action.delete:hover {
        background: var(--brand-red);
        color: white;
    }

    .empty-state {
        text-align: center;
        padding: 80px 24px !important;
        vertical-align: middle;
    }

    .empty-icon {
        display: flex;
        justify-content: center;
        align-items: center;
        margin-bottom: 24px;
        color: var(--text-dim);
        opacity: 0.6;
    }

    .empty-text {
        font-size: 1.5rem;
        font-weight: 800;
        color: var(--text-main);
        margin-bottom: 12px;
        letter-spacing: -0.5px;
    }

    .empty-subtext {
        color: var(--text-secondary);
        font-size: 1rem;
        max-width: 400px;
        margin: 0 auto;
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
        background-image: url(\"data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 24 24' stroke='%236b7280'%3E%3Cpath stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='M19 9l-7 7-7-7'%3E%3C/path%3E%3C/svg%3E\");
        background-repeat: no-repeat;
        background-position: right 12px center;
        background-size: 16px;
        padding-right: 40px;
    }

    .dark .footer-select {
        background-image: url(\"data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 24 24' stroke='%2394a3b8'%3E%3Cpath stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='M19 9l-7 7-7-7'%3E%3C/path%3E%3C/svg%3E\");
    }

    .footer-select:focus {
        outline: none;
        border-color: var(--brand-blue);
        background-color: var(--card-bg);
    }

    .pagination-wrapper nav {
        display: flex;
        justify-content: center;
    }

    .pagination-wrapper .text-sm {
        color: var(--text-secondary) !important;
    }

    .pagination-wrapper .font-medium {
        color: var(--text-main) !important;
    }

    .dark .pagination-wrapper .text-gray-700,
    .dark .pagination-wrapper .text-gray-500 {
        color: var(--text-secondary) !important;
    }

    .pagination-wrapper nav > div:first-child {
        display: flex;
    }

    .pagination-wrapper nav > div:last-child {
        display: none;
    }

    @media (min-width: 640px) {
        .pagination-wrapper nav > div:first-child {
            display: none !important;
        }

        .pagination-wrapper nav > div:last-child {
            display: flex !important;
        }
    }

    .pagination {
        display: flex;
        justify-content: center;
        gap: 4px;
        list-style: none;
        padding: 0;
        margin: 0;
    }

    .page-item .page-link {
        display: flex;
        align-items: center;
        justify-content: center;
        width: 36px;
        height: 36px;
        border-radius: 8px;
        color: var(--text-secondary);
        font-weight: 600;
        font-size: 0.875rem;
        background: var(--hover-bg);
        border: 1px solid transparent;
        transition: all 0.2s;
        text-decoration: none;
    }

    .page-item.active .page-link {
        background: var(--brand-blue);
        color: white;
        box-shadow: 0 4px 6px rgba(59, 130, 246, 0.3);
    }

    .page-item.disabled .page-link {
        opacity: 0.5;
        cursor: not-allowed;
    }

    .page-item:not(.active):not(.disabled) .page-link:hover {
        background: var(--card-bg);
        border-color: var(--brand-blue);
        color: var(--brand-blue);
    }

    .glass {
        backdrop-filter: blur(20px);
    }

    @media (max-width: 768px) {
        .shift-management-container {
            padding: 40px 15px 30px;
        }

        .page-header {
            flex-direction: column;
        }

        .btn-primary {
            width: 100%;
            justify-content: center;
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
