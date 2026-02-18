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
                        <th>No</th>
                        <th>Nama Shift</th>
                        <th>Jam Masuk</th>
                        <th>Jam Pulang</th>
                        <th>Dibuat</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($shifts as $index => $shift)
                    <tr>
                        <td>{{ $shifts->firstItem() + $index }}</td>
                        <td class="font-semibold">{{ $shift->name }}</td>
                        <td>
                            <span class="time-badge in">
                                {{ \Carbon\Carbon::parse($shift->start_time)->format('H:i') }}
                            </span>
                        </td>
                        <td>
                            <span class="time-badge out">
                                {{ \Carbon\Carbon::parse($shift->end_time)->format('H:i') }}
                            </span>
                        </td>
                        <td>{{ $shift->created_at->isoFormat('D MMM Y') }}</td>
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
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="empty-state">
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

        @if($shifts->hasPages())
        <div class="pagination-wrapper">
            <div class="flex justify-between items-center">
                <div class="footer-info">
                   Menampilkan {{ $shifts->firstItem() }} sampai {{ $shifts->lastItem() }} dari {{ $shifts->total() }} entri
                </div>
                <div>
                     <form method="GET" action="{{ route('shifts.index') }}" class="flex items-center gap-2 mr-4">
                        <span class="text-sm text-gray-500">Per halaman:</span>
                        <select name="per_page" onchange="this.form.submit()" class="footer-select">
                            <option value="10" {{ request('per_page', 10) == 10 ? 'selected' : '' }}>10</option>
                            <option value="25" {{ request('per_page') == 25 ? 'selected' : '' }}>25</option>
                            <option value="50" {{ request('per_page') == 50 ? 'selected' : '' }}>50</option>
                        </select>
                        @if(request('search'))
                            <input type="hidden" name="search" value="{{ request('search') }}">
                        @endif
                    </form>
                </div>
                {{ $shifts->links() }}
            </div>
        </div>
        @endif
    </div>
</div>

@push('styles')
<style>
    .shift-management-container {
        padding: 85px 30px 40px;
        max-width: 1400px;
        margin: 0 auto;
    }

    .page-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 30px;
    }

    .page-title {
        font-size: 1.875rem;
        font-weight: 800;
        color: var(--text-main);
        letter-spacing: -0.025em;
    }

    .text-brand {
        color: var(--brand-blue);
    }

    .page-subtitle {
        color: var(--text-secondary);
        margin-top: 4px;
    }

    .btn-primary {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        background: var(--brand-blue);
        color: white;
        padding: 12px 20px;
        border-radius: 12px;
        font-weight: 600;
        transition: all 0.3s;
        box-shadow: 0 4px 6px rgba(59, 130, 246, 0.2);
        text-decoration: none;
    }

    .btn-primary:hover {
        background: var(--brand-blue-dark);
        transform: translateY(-2px);
        box-shadow: 0 6px 12px rgba(59, 130, 246, 0.3);
    }

    .search-section {
        padding: 24px;
        border-radius: 16px;
        margin-bottom: 30px;
    }

    .search-form {
        display: flex;
        gap: 12px;
    }

    .search-input-wrapper {
        flex: 1;
        position: relative;
    }

    .search-icon {
        position: absolute;
        left: 14px;
        top: 50%;
        transform: translateY(-50%);
        color: var(--text-dim);
    }

    .search-input {
        width: 100%;
        padding: 12px 12px 12px 44px;
        background: var(--hover-bg);
        border: 1px solid var(--card-border);
        border-radius: 12px;
        color: var(--text-main);
        transition: all 0.3s;
    }

    .search-input:focus {
        border-color: var(--brand-blue);
        box-shadow: 0 0 0 4px rgba(59, 130, 246, 0.1);
        outline: none;
    }

    .btn-search {
        background: var(--text-main);
        color: white;
        padding: 0 24px;
        border-radius: 12px;
        font-weight: 600;
        border: none;
        cursor: pointer;
    }

    .btn-reset {
        display: inline-flex;
        align-items: center;
        color: var(--text-secondary);
        padding: 0 16px;
        font-weight: 500;
        text-decoration: none;
    }

    .table-container {
        border-radius: 20px;
        overflow: hidden;
    }

    .data-table {
        width: 100%;
        border-collapse: collapse;
    }

    .data-table th {
        background: var(--hover-bg);
        padding: 16px 24px;
        text-align: left;
        font-size: 0.8125rem;
        font-weight: 700;
        text-transform: uppercase;
        color: var(--text-secondary);
        letter-spacing: 0.05em;
    }

    .data-table td {
        padding: 16px 24px;
        border-bottom: 1px solid var(--card-border);
        color: var(--text-main);
    }

    .data-table tr:last-child td {
        border-bottom: none;
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
        color: #10b981;
    }

    .time-badge.out {
        background: rgba(245, 158, 11, 0.1);
        color: #f59e0b;
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
        border: none;
        cursor: pointer;
    }

    .btn-action.delete:hover {
        background: var(--brand-red);
        color: white;
    }

    .empty-state {
        text-align: center;
        padding: 60px 0 !important;
    }

    .empty-icon {
        color: var(--text-dim);
        margin-bottom: 16px;
    }

    .empty-text {
        font-size: 1.125rem;
        font-weight: 700;
    }

    .empty-subtext {
        color: var(--text-secondary);
    }

    .pagination-wrapper {
        padding: 20px 24px;
        border-top: 1px solid var(--card-border);
    }

    .footer-select {
        padding: 4px 8px;
        border-radius: 6px;
        border: 1px solid var(--card-border);
        background: var(--card-bg);
        color: var(--text-main);
    }

    .alert-success {
        padding: 16px 24px;
        border-radius: 12px;
        background: rgba(16, 185, 129, 0.1);
        color: #10b981;
        display: flex;
        align-items: center;
        gap: 12px;
        border-left: 4px solid #10b981;
    }

    .glass {
        background: var(--card-bg);
        border: 1px solid var(--card-border);
        box-shadow: var(--shadow-sm);
    }
</style>
@endpush
@endsection
