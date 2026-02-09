@extends('layouts.app')

@section('content')
<div class="unit-management-container">
    
    <!-- Header -->
    <div class="page-header">
        <div>
            <h1 class="page-title">Manajemen <span class="text-brand">Unit</span></h1>
            <p class="page-subtitle">Kelola unit kerja dan shift yang tersedia</p>
        </div>
        <a href="{{ route('units.create') }}" class="btn-primary">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
            Tambah Unit
        </a>
    </div>

    <!-- Success Message -->
    @if(session('success'))
    <div class="alert-success">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
        </svg>
        {{ session('success') }}
    </div>
    @endif

    <!-- Units Table -->
    <div class="table-container glass">
        <div class="table-wrapper">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Nama Unit</th>
                        <th>Shift Tersedia</th>
                        <th>Dibuat</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($units as $index => $unit)
                    <tr>
                        <td>{{ $units->firstItem() + $index }}</td>
                        <td class="font-semibold">{{ $unit->name }}</td>
                        <td>
                            @if($unit->available_shifts && count($unit->available_shifts) > 0)
                                <div class="shifts-container">
                                    @foreach($unit->available_shifts as $shift)
                                        <span class="shift-badge-small">
                                            @if(is_array($shift))
                                                {{ $shift['name'] }} ({{ $shift['start_time'] }} - {{ $shift['end_time'] }})
                                            @else
                                                {{ $shift }}
                                            @endif
                                        </span>
                                    @endforeach
                                </div>
                            @else
                                <span class="text-dim">Belum diatur</span>
                            @endif
                        </td>
                        <td>{{ $unit->created_at->isoFormat('D MMM Y') }}</td>
                        <td>
                            <div class="action-buttons">
                                <a href="{{ route('units.edit', $unit) }}" class="btn-action edit">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                    </svg>
                                </a>
                                <form action="{{ route('units.destroy', $unit) }}" method="POST" class="inline-block" onsubmit="return confirm('Yakin ingin menghapus unit ini?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn-action delete">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                        </svg>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="empty-state">
                            <div class="empty-icon">
                                <svg class="w-16 h-16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                                </svg>
                            </div>
                            <p class="empty-text">Belum ada data unit</p>
                            <p class="empty-subtext">Klik tombol "Tambah Unit" untuk menambahkan unit baru</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        @if($units->hasPages())
        <div class="pagination-wrapper">
            {{ $units->links() }}
        </div>
        @endif
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
    }

    .unit-management-container {
        max-width: 1400px;
        margin: 0 auto;
        padding: 60px 20px 40px;
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
    }

    .btn-primary:hover {
        background: var(--brand-blue-dark);
        transform: translateY(-2px);
        box-shadow: 0 10px 20px rgba(59, 130, 246, 0.3);
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

    .shifts-container {
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

    .text-dim {
        color: var(--text-dim);
        font-style: italic;
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

    .pagination-wrapper {
        padding: 20px;
        border-top: 1px solid var(--card-border);
    }

    /* Pagination Customization */
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
        .unit-management-container {
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
