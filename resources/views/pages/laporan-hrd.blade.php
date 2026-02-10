@extends('layouts.app')

@push('scripts')
<link href="https://cdn.jsdelivr.net/npm/tom-select@2.4.1/dist/css/tom-select.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/tom-select@2.4.1/dist/js/tom-select.complete.min.js"></script>
@endpush

@section('content')
<div class="hrd-report-container" x-data="{ 
    showFilters: true,
    selectedStatus: '{{ request('status') }}',
    selectedUnit: '{{ request('unit') }}',
    startDate: '{{ request('start_date', \Carbon\Carbon::now()->startOfMonth()->format('Y-m-d')) }}',
    endDate: '{{ request('end_date', \Carbon\Carbon::now()->format('Y-m-d')) }}'
}">
    
    <!-- Header -->
    <div class="report-header">
        <div>
            <h1 class="page-title">Laporan <span class="text-brand">Kehadiran</span></h1>
            <p class="page-subtitle">Monitoring dan analisis kehadiran karyawan</p>
        </div>
        <div class="header-actions">
            <button @click="showFilters = !showFilters" class="btn-filter">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"/>
                </svg>
                <span x-text="showFilters ? 'Sembunyikan Filter' : 'Tampilkan Filter'"></span>
            </button>
        </div>
    </div>

    <!-- Filter Section -->
    <div x-show="showFilters" x-transition class="filter-section glass">
        <form method="GET" action="{{ route('hrd.report') }}" class="filter-form">
            <div class="filter-grid">
                <!-- Date Range & Status -->
                <div class="filter-group">
                    <label class="filter-label">Tanggal Mulai</label>
                    <input type="date" name="start_date" x-model="startDate" class="filter-input">
                </div>
                
                <div class="filter-group">
                    <label class="filter-label">Tanggal Akhir</label>
                    <input type="date" name="end_date" x-model="endDate" class="filter-input">
                </div>

                <div class="filter-group">
                    <label class="filter-label">Status Kehadiran</label>
                    <select name="status" x-model="selectedStatus" class="filter-input">
                        <option value="">Semua Status</option>
                        <option value="pending">Menunggu Persetujuan</option>
                        <option value="hadir">Hadir</option>
                        <option value="terlambat">Terlambat</option>
                        <option value="tidak_hadir">Tidak Hadir</option>
                    </select>
                </div>

                <!-- Unit & Employee -->
                <div class="filter-group grid-span-unit">
                    <label class="filter-label">Unit/Departemen</label>
                    <select name="unit" x-model="selectedUnit" class="filter-input">
                        <option value="">Semua Unit</option>
                        @foreach($units as $unit)
                            <option value="{{ $unit }}">{{ $unit }}</option>
                        @endforeach
                    </select>
                </div>
                
                <div class="filter-group grid-span-employee">
                    <label class="filter-label">Pilih Pegawai (Searchable)</label>
                    <select id="employee-select" name="user_id" class="filter-input">
                        <option value="">Semua Pegawai</option>
                        @foreach($users as $user)
                            <option value="{{ $user->id }}" {{ request('user_id') == $user->id ? 'selected' : '' }}>
                                {{ $user->name }} ({{ $user->nip }})
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="filter-actions">
                <button type="submit" class="btn-primary">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                    Terapkan Filter
                </button>
                <a href="{{ route('hrd.report') }}" class="btn-secondary">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                    </svg>
                    Reset
                </a>
            </div>
        </form>
    </div>

    <!-- Export Buttons -->
    <div class="export-section">
        <div class="export-info flex gap-4">
            <span class="result-count">{{ $presences->total() }} Data Ditemukan</span>
            <span class="total-attendance px-3 py-1 bg-blue-100 text-blue-700 rounded-lg text-sm font-semibold">
                Total Kehadiran: {{ $totalAttendance }} Hari
            </span>
        </div>
        <div class="export-buttons">
            <a href="{{ route('hrd.export.excel', request()->query()) }}" class="btn-export excel">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
                Export Excel
            </a>
            <!-- <a href="{{ route('hrd.export.pdf', request()->query()) }}" class="btn-export pdf">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                </svg>
                Export PDF
            </a> -->
        </div>
    </div>

    <!-- Data Table -->
    <div class="table-container glass">
        <div class="table-wrapper">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Tanggal</th>
                        <th>Nama</th>
                        <th>Unit</th>
                        <th>Total Hadir</th>
                        <th>Shift</th>
                        <th>Jam Masuk</th>
                        <th>Jam Keluar</th>
                        <th>Status</th>
                        <th>Keterangan</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($presences as $index => $presence)
                    <tr>
                        <td>{{ $presences->firstItem() + $index }}</td>
                        <td>{{ \Carbon\Carbon::parse($presence->date)->isoFormat('D MMM Y') }}</td>
                        <td class="font-semibold">{{ $presence->user->name ?? 'N/A' }}</td>
                        <td><span class="unit-badge">{{ $presence->user->unit ?? '-' }}</span></td>
                        <td>
                            <span class="text-sm font-bold text-blue-600 bg-blue-50 dark:bg-blue-900/30 dark:text-blue-400 px-2 py-1 rounded">
                                {{ $userAttendanceCounts[$presence->user_id] ?? 0 }} Hari
                            </span>
                        </td>
                        <td><span class="shift-name-badge">{{ $presence->shift_name ?? '-' }}</span></td>
                        <td>
                            @if($presence->time_in)
                                <span class="time-badge">
                                    {{ \Carbon\Carbon::parse($presence->time_in)->format('H:i') }}
                                </span>
                            @else
                                <span class="badge-empty">-</span>
                            @endif
                        </td>
                        <td>
                            @if($presence->time_out)
                                <span class="time-badge">
                                    {{ \Carbon\Carbon::parse($presence->time_out)->format('H:i') }}
                                </span>
                            @else
                                <span class="badge-empty">-</span>
                            @endif
                        </td>
                        <td>
                            @if($presence->is_pending)
                                <span class="status-badge pending">Perlu Approval</span>
                                <form action="{{ route('hrd.approve', $presence->id) }}" method="POST" class="inline-block ml-2">
                                    @csrf
                                    <button type="submit" class="text-xs bg-green-500 text-white px-2 py-1 rounded hover:bg-green-600" onclick="return confirm('Setujui presensi ini?')">
                                        Accept
                                    </button>
                                </form>
                            @elseif($presence->time_in)
                                <span class="status-badge {{ $presence->status === 'Terlambat' ? 'late' : 'present' }}">
                                    {{ $presence->status ?? 'Hadir' }}
                                </span>
                            @else
                                <span class="status-badge absent">Tidak Hadir</span>
                            @endif
                        </td>
                        <td class="note-cell">{{ $presence->note ?? '-' }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="10" class="empty-state">
                            <div class="empty-content">
                                <div class="empty-icon">
                                    <svg class="w-16 h-16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                    </svg>
                                </div>
                                <p class="empty-text">Tidak ada data kehadiran</p>
                                <p class="empty-subtext">Coba ubah filter atau rentang tanggal</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Footer: Pagination & Per Page -->
        <div class="table-footer glass">
            <div class="per-page-footer">
                <form action="{{ route('hrd.report') }}" method="GET" id="perPageFormHrd">
                    @foreach(request()->except(['per_page', 'page']) as $key => $value)
                        <input type="hidden" name="{{ $key }}" value="{{ $value }}">
                    @endforeach
                    <label for="per_page">Tampilkan:</label>
                    <select name="per_page" onchange="this.form.submit()" class="footer-select">
                        <option value="10" {{ request('per_page') == 10 ? 'selected' : '' }}>10</option>
                        <option value="25" {{ request('per_page') == 25 ? 'selected' : '' }}>25</option>
                        <option value="50" {{ request('per_page') == 50 ? 'selected' : '' }}>50</option>
                        <option value="100" {{ request('per_page') == 100 ? 'selected' : '' }}>100</option>
                    </select>
                </form>
            </div>

            @if($presences->hasPages())
            <div class="pagination-wrapper">
                {{ $presences->links() }}
            </div>
            @endif
        </div>
    </div>
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        if (typeof TomSelect !== 'undefined') {
            new TomSelect('#employee-select', {
                create: false,
                sortField: {
                    field: 'text',
                    direction: 'asc'
                },
                placeholder: 'Cari Nama atau NIP...',
                allowEmptyOption: true,
                maxOptions: null,
            });
        }
    });
</script>
<style>
    /* Tom Select Custom Styling for Glass UI */
    .ts-wrapper .ts-control {
        background: var(--hover-bg) !important;
        border: 1px solid var(--card-border) !important;
        border-radius: 12px !important;
        color: var(--text-main) !important;
        padding: 12px 16px !important;
        font-family: 'Outfit', sans-serif !important;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1) !important;
        min-height: 48px !important;
        display: flex !important;
        align-items: center !important;
        cursor: text !important;
    }

    .ts-wrapper .ts-control input {
        color: var(--text-main) !important;
        font-family: 'Outfit', sans-serif !important;
        font-size: 1rem !important;
    }

    .ts-wrapper.focus .ts-control {
        border-color: var(--brand-blue) !important;
        box-shadow: 0 0 0 4px rgba(59, 130, 246, 0.15) !important;
        background: var(--card-bg) !important;
    }

    .ts-dropdown {
        background: var(--glass-bg) !important;
        backdrop-filter: blur(20px) !important;
        border: 1px solid var(--card-border) !important;
        border-radius: 16px !important;
        color: var(--text-main) !important;
        box-shadow: 0 10px 40px var(--shadow-color) !important;
        margin-top: 8px !important;
        padding: 8px !important;
        z-index: 1000 !important;
    }

    .ts-dropdown .option {
        padding: 10px 14px !important;
        border-radius: 10px !important;
        font-weight: 500 !important;
        transition: all 0.2s !important;
    }

    .ts-dropdown .active {
        background: var(--brand-blue) !important;
        color: white !important;
    }

    .ts-dropdown .option:hover:not(.active) {
        background: var(--hover-bg) !important;
        color: var(--brand-blue) !important;
    }

    .ts-control .item {
        color: var(--text-main) !important;
        font-weight: 600 !important;
    }

    .ts-wrapper.single .ts-control {
        background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 24 24' stroke='%236b7280'%3E%3Cpath stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='M19 9l-7 7-7-7'%3E%3C/path%3E%3C/svg%3E") !important;
        background-repeat: no-repeat !important;
        background-position: right 12px center !important;
        background-size: 16px !important;
    }

    .dark .ts-wrapper.single .ts-control {
        background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 24 24' stroke='%2394a3b8'%3E%3Cpath stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='M19 9l-7 7-7-7'%3E%3C/path%3E%3C/svg%3E") !important;
    }

    .ts-wrapper .ts-control input::placeholder {
        color: var(--text-dim) !important;
    }

    :root {
        --brand-blue: #3b82f6;
        --brand-blue-dark: #2563eb;
        --brand-green: #10b981;
        --brand-yellow: #f59e0b;
        --brand-red: #ef4444;
        
        /* Light mode */
        --text-main: #1e293b;
        --text-secondary: #64748b;
        --text-dim: #94a3b8;
        --bg-primary: #ffffff;
        --card-bg: #ffffff;
        --card-border: rgba(0, 0, 0, 0.08);
        --glass-bg: rgba(255, 255, 255, 0.8);
        --hover-bg: rgba(0, 0, 0, 0.03);
        --shadow-color: rgba(0, 0, 0, 0.1);
    }

    .dark {
        /* Dark mode */
        --text-main: #f8fafc;
        --text-secondary: #94a3b8;
        --text-dim: #64748b;
        --bg-primary: #0c111d;
        --card-bg: #1f2937;
        --card-border: rgba(255, 255, 255, 0.08);
        --glass-bg: rgba(31, 41, 55, 0.8);
        --hover-bg: rgba(255, 255, 255, 0.05);
        --shadow-color: rgba(0, 0, 0, 0.3);
    }

    .hrd-report-container {
        max-width: 1400px;
        margin: 0 auto;
        padding: 60px 20px 40px;
        font-family: 'Outfit', sans-serif;
    }

    .report-header {
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

    .header-actions {
        display: flex;
        gap: 12px;
    }

    .btn-filter {
        display: flex;
        align-items: center;
        gap: 8px;
        padding: 12px 24px;
        background: var(--glass-bg);
        border: 1px solid var(--card-border);
        border-radius: 12px;
        color: var(--text-main);
        font-weight: 600;
        cursor: pointer;
        transition: all 0.3s;
        backdrop-filter: blur(10px);
    }

    .btn-filter:hover {
        background: var(--hover-bg);
        border-color: var(--brand-blue);
        transform: translateY(-2px);
    }

    .filter-section {
        background: var(--glass-bg);
        border: 1px solid var(--card-border);
        border-radius: 24px;
        padding: 30px;
        margin-bottom: 30px;
        backdrop-filter: blur(20px);
        position: relative;
        z-index: 50; /* Ensure filter section stays above table */
    }

    .filter-form {
        display: flex;
        flex-direction: column;
        gap: 24px;
    }

    .filter-grid {
        display: grid;
        grid-template-columns: 1fr;
        gap: 20px;
    }

    @media (min-width: 768px) {
        .filter-grid {
            grid-template-columns: repeat(2, 1fr);
        }
    }

    @media (min-width: 1024px) {
        .filter-grid {
            grid-template-columns: repeat(3, 1fr);
        }
    }

    .grid-span-unit {
        grid-column: span 1;
    }

    @media (min-width: 768px) {
        .grid-span-employee {
            grid-column: span 2;
        }
    }

    @media (min-width: 1024px) {
        .grid-span-employee {
            grid-column: span 2;
        }
    }

    .filter-group {
        display: flex;
        flex-direction: column;
        gap: 8px;
    }

    .filter-label {
        font-size: 0.875rem;
        font-weight: 700;
        color: var(--text-secondary);
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .filter-input {
        padding: 12px 16px;
        background: var(--hover-bg);
        border: 1px solid var(--card-border);
        border-radius: 12px;
        color: var(--text-main);
        font-size: 1rem;
        transition: all 0.3s;
    }

    .filter-input:focus {
        outline: none;
        border-color: var(--brand-blue);
        background: var(--card-bg);
    }

    .filter-actions {
        display: flex;
        gap: 12px;
        justify-content: flex-end;
        flex-wrap: wrap; /* Ensure buttons wrap on small screens */
    }

    .btn-primary, .btn-secondary {
        display: flex;
        align-items: center;
        gap: 8px;
        padding: 12px 24px;
        border-radius: 12px;
        font-weight: 700;
        cursor: pointer;
        transition: all 0.3s;
        text-decoration: none;
    }

    .btn-primary {
        background: var(--brand-blue);
        color: white;
        border: none;
    }

    .btn-primary:hover {
        background: var(--brand-blue-dark);
        transform: translateY(-2px);
        box-shadow: 0 10px 20px rgba(59, 130, 246, 0.3);
    }

    .btn-secondary {
        background: var(--hover-bg);
        color: var(--text-main);
        border: 1px solid var(--card-border);
    }

    .btn-secondary:hover {
        background: var(--card-bg);
        border-color: var(--brand-blue);
    }

    .export-section {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 24px;
        flex-wrap: wrap;
        gap: 16px;
    }

    .result-count {
        font-weight: 700;
        color: var(--text-secondary);
        font-size: 0.875rem;
    }

    .total-attendance {
        background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);
        color: white;
        padding: 6px 16px;
        border-radius: 20px;
        font-weight: 700;
        font-size: 0.875rem;
        box-shadow: 0 4px 6px -1px rgba(59, 130, 246, 0.4);
        border: 1px solid rgba(255, 255, 255, 0.2);
    }

    .export-buttons {
        display: flex;
        gap: 12px;
    }

    .btn-export {
        display: flex;
        align-items: center;
        gap: 8px;
        padding: 10px 20px;
        border-radius: 12px;
        font-weight: 600;
        font-size: 0.875rem;
        cursor: pointer;
        transition: all 0.3s;
        text-decoration: none;
        border: 1px solid;
    }

    .btn-export.excel {
        background: rgba(16, 185, 129, 0.1);
        color: var(--brand-green);
        border-color: rgba(16, 185, 129, 0.3);
    }

    .btn-export.excel:hover {
        background: var(--brand-green);
        color: white;
        transform: translateY(-2px);
    }

    .btn-export.pdf {
        background: rgba(239, 68, 68, 0.1);
        color: var(--brand-red);
        border-color: rgba(239, 68, 68, 0.3);
    }

    .btn-export.pdf:hover {
        background: var(--brand-red);
        color: white;
        transform: translateY(-2px);
    }

    .table-container {
        background: var(--glass-bg);
        border: 1px solid var(--card-border);
        border-radius: 24px;
        overflow: visible; /* Important: allow dropdown to overflow table container */
        backdrop-filter: blur(20px);
        position: relative;
        z-index: 10;
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

    .shift-name-badge {
        display: inline-block;
        padding: 4px 10px;
        background: rgba(59, 130, 246, 0.1);
        color: var(--brand-blue);
        border-radius: 6px;
        font-weight: 600;
        font-size: 0.75rem;
        white-space: nowrap;
    }

    .time-badge {
        display: inline-block;
        padding: 4px 12px;
        background: var(--hover-bg);
        border-radius: 8px;
        font-weight: 600;
        font-size: 0.875rem;
    }

    .badge-empty {
        color: var(--text-dim);
        font-size: 0.875rem;
    }

    .unit-badge {
        display: inline-block;
        padding: 4px 12px;
        background: rgba(59, 130, 246, 0.1);
        color: var(--brand-blue);
        border-radius: 8px;
        font-weight: 600;
        font-size: 0.875rem;
    }

    .status-badge {
        display: inline-block;
        padding: 4px 12px;
        border-radius: 8px;
        font-weight: 700;
        font-size: 0.75rem;
        text-transform: uppercase;
    }

    .status-badge.present {
        background: rgba(16, 185, 129, 0.1);
        color: var(--brand-green);
    }

    .status-badge.late {
        background: rgba(245, 158, 11, 0.1);
        color: var(--brand-yellow);
    }

    .status-badge.absent {
        background: rgba(239, 68, 68, 0.1);
        color: var(--brand-red);
    }
    
    .status-badge.pending {
        background: rgba(245, 158, 11, 0.1);
        color: var(--brand-yellow);
        border: 1px solid var(--brand-yellow);
    }

    .note-cell {
        max-width: 200px;
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
        color: var(--text-secondary);
        font-size: 0.875rem;
    }

    .empty-state {
        text-align: center;
        padding: 80px 24px !important;
        vertical-align: middle;
    }

    .empty-content {
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        width: 100%;
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

    /* Hide the mobile view on desktop and vice versa */
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

    .glass {
        backdrop-filter: blur(20px);
    }

    @media (max-width: 768px) {
        .hrd-report-container {
            padding: 40px 15px 30px;
        }

        .report-header {
            flex-direction: column;
        }

        .filter-grid {
            grid-template-columns: 1fr;
        }

        .filter-actions {
            flex-direction: column;
        }

        .btn-primary, .btn-secondary {
            width: 100%;
            justify-content: center;
        }

        .export-section {
            flex-direction: column;
            align-items: flex-start;
        }

        .export-buttons {
            width: 100%;
            flex-direction: column;
        }

        .btn-export {
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
