@extends('layouts.app')
@section('title', 'Riwayat Lembur')

@section('content')
<div class="hrd-report-container animate-fade-in">
    <!-- Header Content -->
    <div class="report-header">
        <div class="header-main">
            <h1 class="page-title">Riwayat <span class="text-brand">Lembur</span></h1>
            <p class="page-subtitle">Pantau status dan riwayat pengajuan lembur Anda.</p>
        </div>
        <div class="header-actions">
            <a href="{{ route('overtime.create') }}" class="btn-primary flex items-center gap-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M12 4v16m8-8H4" stroke-width="2.5" stroke-linecap="round"/></svg>
                <span>Ajukan Lembur</span>
            </a>
        </div>
    </div>

    <div class="stats-grid">
        <div class="stat-card card-gradient shadow-premium">
            <div class="card-blur-circle"></div>
            <p class="stat-label">Total Pengajuan</p>
            <div class="stat-main">
                 <h3 class="stat-value">{{ $stats['total'] }}</h3>
                 <span class="stat-unit">kali diajukan</span>
            </div>
            <div class="stat-badge-pill">Periode: {{ date('F Y') }}</div>
        </div>

        <div class="stat-group">
            <div class="stat-card glass shadow-premium">
                <p class="stat-label text-dim">Status Disetujui</p>
                <div class="stat-item-row">
                    <div class="icon-box emerald shadow-emerald-light">
                        <svg class="h-8 w-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M5 13l4 4L19 7" stroke-width="2.5" stroke-linecap="round"/></svg>
                    </div>
                    <div>
                        <h4 class="stat-time text-main">{{ $stats['approved'] }}</h4>
                        <p class="stat-date text-dim">Pengajuan Telah Divalidasi</p>
                    </div>
                </div>
            </div>

            <div class="stat-card glass shadow-premium">
                <p class="stat-label text-dim">Dalam Antrean</p>
                <div class="stat-item-row">
                    <div class="icon-box orange shadow-orange-light">
                        <svg class="h-8 w-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" stroke-width="2.5" stroke-linecap="round"/></svg>
                    </div>
                    <div>
                        <h4 class="stat-time text-main">{{ $stats['pending'] }}</h4>
                        <p class="stat-date text-dim">Menunggu Verifikasi Admin</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @if(session('success'))
        <div class="alert-premium alert-success animate-bounce-short mb-8">
            <div class="alert-icon-wrapper">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M5 13l4 4L19 7" stroke-width="3" stroke-linecap="round"/></svg>
            </div>
            <span>{{ session('success') }}</span>
        </div>
    @endif

    <!-- Filters -->
    <div class="filter-section glass shadow-premium mb-8">
        <form action="{{ route('overtime.index') }}" method="GET" class="filter-form">
            <div class="filter-grid">
                <div class="filter-group">
                    <label class="filter-label">Status</label>
                    <select name="status" class="filter-input">
                        <option value="">Semua Status</option>
                        <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                        <option value="approved" {{ request('status') == 'approved' ? 'selected' : '' }}>Disetujui</option>
                        <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}>Ditolak</option>
                    </select>
                </div>
                <div class="filter-group">
                    <label class="filter-label">Mulai Tanggal</label>
                    <input type="date" name="start_date" value="{{ request('start_date') }}" class="filter-input">
                </div>
                <div class="filter-group">
                    <label class="filter-label">Sampai Tanggal</label>
                    <input type="date" name="end_date" value="{{ request('end_date') }}" class="filter-input">
                </div>
            </div>
            <div class="filter-actions mt-4">
                <button type="submit" class="btn-primary">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" stroke-width="3"/></svg>
                    <span>Filter Data</span>
                </button>
                @if(request()->anyFilled(['status', 'start_date', 'end_date']))
                    <a href="{{ route('overtime.index') }}" class="btn-secondary">Reset</a>
                @endif
            </div>
        </form>
    </div>

    <!-- Content Table -->
    <div class="table-container glass shadow-premium">
        <div class="card-body p-0">
            <div class="table-wrapper">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th style="width: 80px;">Aksi</th>
                            <th>Waktu & Tanggal</th>
                            <th>Alasan Lembur</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($overtimes as $ot)
                            @php
                                $start = \Carbon\Carbon::parse($ot->start_time);
                                $end = \Carbon\Carbon::parse($ot->end_time);
                                if ($end->lt($start)) $end->addDay();
                                $duration = $start->diffInMinutes($end);
                                $hours = floor($duration / 60);
                                $mins = $duration % 60;
                            @endphp
                            <tr>
                                {{-- Aksi --}}
                                <td>
                                    <div class="action-buttons">
                                        <button type="button" class="btn-action view" title="Detail" 
                                            onclick="Swal.fire({
                                                title: 'Detail Lembur',
                                                html: `<div class='text-left space-y-3'>
                                                    <div><label class='text-[10px] font-black uppercase text-secondary'>Alasan:</label><p class='text-sm'>{{ addslashes($ot->reason) }}</p></div>
                                                    @if($ot->admin_note)
                                                    <div><label class='text-[10px] font-black uppercase text-secondary'>Catatan Admin:</label><p class='text-sm italic text-blue-600'>{{ addslashes($ot->admin_note) }}</p></div>
                                                    @endif
                                                </div>`,
                                                confirmButtonText: 'Tutup',
                                                confirmButtonColor: '#3b82f6',
                                                customClass: { popup: 'rounded-3xl' }
                                            })">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                            </svg>
                                        </button>
                                    </div>
                                </td>

                                {{-- Waktu & Tanggal --}}
                                <td>
                                    <div class="flex flex-col gap-1.5">
                                        <span class="font-bold text-main">{{ \Carbon\Carbon::parse($ot->date)->isoFormat('D MMM Y') }}</span>
                                        <div class="attendance-stack">
                                            <div class="stack-item in">
                                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M11 16l-4-4m0 0l4-4m-4 4h14" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
                                                <span class="time">{{ substr($ot->start_time, 0, 5) }}</span>
                                            </div>
                                            <div class="stack-item out">
                                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M17 16l4-4m0 0l-4-4m4 4H7" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
                                                <span class="time">{{ substr($ot->end_time, 0, 5) }}</span>
                                            </div>
                                        </div>
                                    </div>
                                </td>

                                {{-- Alasan & Note --}}
                                <td>
                                    <div class="flex flex-col gap-1">
                                        <div class="truncate-2-lines max-w-xs text-secondary text-sm">
                                            {{ $ot->reason }}
                                        </div>
                                        @if($ot->admin_note)
                                            <div class="flex items-center gap-1.5 px-2 py-1 rounded bg-blue-50 dark:bg-blue-900/20 text-[10px] text-blue-600 dark:text-blue-400 border border-blue-100 dark:border-blue-800">
                                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M7 8h10M7 12h4m1 8l-4-4H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-3l-4 4z" stroke-width="2"/></svg>
                                                <span class="font-bold truncate">{{ $ot->admin_note }}</span>
                                            </div>
                                        @endif
                                    </div>
                                </td>

                                {{-- Status --}}
                                <td>
                                    @if($ot->status === 'approved')
                                        <span class="status-pill status-approved">Disetujui</span>
                                    @elseif($ot->status === 'rejected')
                                        <span class="status-pill status-rejected">Ditolak</span>
                                    @else
                                        <span class="status-pill status-pending">Pending</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="empty-state">
                                    <div class="empty-icon-wrapper">
                                        <svg class="w-16 h-16 text-dim opacity-20" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" stroke-width="1.5"/></svg>
                                    </div>
                                    <p class="empty-text">Belum ada riwayat pengajuan lembur</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="table-footer glass">
                <div class="per-page-footer">
                    <form action="{{ route('overtime.index') }}" method="GET" id="perPageForm">
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

                @if($overtimes->hasPages())
                <div class="pagination-wrapper">
                    {{ $overtimes->links() }}
                </div>
                @endif
            </div>
        </div>
    </div>
</div>

<style>
    :root {
        --brand-blue: #3b82f6;
        --brand-blue-dark: #2563eb;
        --brand-green: #10b981;
        --brand-yellow: #f59e0b;
        --brand-red: #ef4444;
        --brand-orange: #f97316;
        
        --hover-bg: rgba(0, 0, 0, 0.03);
        --header-bg: #f8fafc;
        --shadow-color: rgba(0, 0, 0, 0.05);
        --brand-blue-hover: #1d4ed8;
        --brand-emerald: #10b981;
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
        --brand-blue-hover: #3b82f6;
        --brand-emerald: #10b981;
    }

    .glass {
        background: var(--glass-bg);
        border: 1px solid var(--card-border);
        backdrop-filter: blur(20px);
        -webkit-backdrop-filter: blur(20px);
    }

    .data-table thead {
        position: sticky;
        top: 0;
        z-index: 20;
        background: var(--header-bg);
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

    .data-table th { padding: 22px 30px; text-align: left; font-size: 10px; font-weight: 900; text-transform: uppercase; letter-spacing: 2px; color: var(--text-dim); }
    .data-table td { padding: 22px 30px; border-bottom: 1px solid var(--card-border); transition: all 0.2s; vertical-align: middle; color: var(--text-main); }

    .data-table tbody tr { transition: all 0.2s ease; }
    .data-table tbody tr:hover td { background: var(--hover-bg) !important; }

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

    .btn-action {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 32px;
        height: 32px;
        border-radius: 8px;
        background: rgba(59, 130, 246, 0.1);
        color: var(--brand-blue);
        border: none;
        cursor: pointer;
        transition: all 0.2s;
    }
    .btn-action:hover {
        background: var(--brand-blue);
        color: white;
    }


    .hrd-report-container { max-width: 1200px; margin: 0 auto; padding: 20px 20px 40px; font-family: 'Outfit', sans-serif; }
    .report-header { display: flex; justify-content: space-between; align-items: flex-end; gap: 30px; margin-bottom: 40px; flex-wrap: wrap; }
    .page-title { font-size: clamp(2rem, 5vw, 3.5rem); font-weight: 900; letter-spacing: -2px; line-height: 1; color: var(--text-main); margin:0;}
    .page-subtitle { color: var(--text-dim); font-size: 1rem; margin-top: 10px; }
    .text-brand { color: var(--brand-blue); }

    .btn-primary { display: flex; align-items: center; gap: 10px; padding: 14px 28px; border-radius: 18px; background: var(--brand-blue); color: white; font-weight: 800; text-transform: uppercase; font-size: 0.875rem; letter-spacing: 1px; transition: all 0.3s cubic-bezier(0.175, 0.885, 0.32, 1.275); border:none; text-decoration:none;}
    .btn-primary:hover { transform: translateY(-5px) scale(1.02); box-shadow: 0 15px 30px rgba(59, 130, 246, 0.3); background: var(--brand-blue-hover); color:white;}

    .stats-grid { display: grid; grid-template-columns: 1.2fr 1.8fr; gap: 30px; margin-bottom: 40px; }
    .stat-card { border-radius: 40px; padding: 40px; position: relative; overflow: hidden; background: var(--card-bg); border: 1px solid var(--card-border); transition: all 0.3s ease; }
    .card-gradient { background: linear-gradient(135deg, var(--brand-blue), var(--brand-blue-hover)); color: white; border: none; }
    .card-blur-circle { position: absolute; width: 300px; height: 300px; border-radius: 50%; background: rgba(255,255,255,0.15); top: -100px; right: -100px; filter: blur(60px); }
    .stat-main { margin-top: 10px; }
    .stat-value { font-size: 6rem; font-weight: 900; letter-spacing: -5px; line-height: 1; margin: 0; color: inherit; }
    .stat-unit { font-size: 1.5rem; font-weight: 700; opacity: 0.5; font-style: italic; }
    .stat-label { color: var(--text-dim); font-size: 0.875rem; font-weight: 600; text-transform: uppercase; letter-spacing: 1px; }
    .card-gradient .stat-label { color: rgba(255,255,255,0.7); }
    .stat-group { display: grid; grid-template-columns: 1fr 1fr; gap: 30px; }
    .stat-item-row { display: flex; align-items: center; gap: 15px; margin-top: 10px; }
    .icon-box { width: 70px; height: 70px; border-radius: 25px; display: flex; align-items: center; justify-content: center; }
    .icon-box.orange { background: rgba(249, 115, 22, 0.1); color: var(--brand-orange); }
    .icon-box.emerald { background: rgba(16, 185, 129, 0.1); color: var(--brand-emerald); }
    .stat-time { font-size: 2.5rem; font-weight: 900; letter-spacing: -1.5px; color: var(--text-main); }
    .stat-date { color: var(--text-dim); font-size: 0.875rem; margin-top: 4px; }
    .stat-badge-pill { display: inline-block; padding: 4px 12px; background: rgba(255,255,255,0.2); border-radius: 20px; font-size: 0.75rem; font-weight: 600; margin-top: 10px; }

    .table-container { border-radius: 35px; overflow: hidden; background: var(--card-bg); border: 1px solid var(--card-border); }
    .table-wrapper { width: 100%; overflow-x: auto; -webkit-overflow-scrolling: touch; }
    .data-table { width: 100%; border-collapse: separate; border-spacing: 0; min-width: 800px; }

    .filter-section { padding: 30px; border-radius: 30px; background: var(--card-bg); border: 1px solid var(--card-border); box-shadow: var(--shadow-color); }
    .filter-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(180px, 1fr)); gap: 20px; }
    .filter-label { font-size: 9px; font-weight: 900; text-transform: uppercase; letter-spacing: 1px; color: var(--text-dim); margin-bottom: 8px; display: block; }
    .filter-input { width: 100%; padding: 12px 18px; border-radius: 12px; background: var(--hover-bg); border: 1px solid var(--card-border); color: var(--text-main); font-weight: 600; font-size: 0.875rem; transition: all 0.3s; }
    .filter-input:focus { outline: none; border-color: var(--brand-blue); background: var(--card-bg); }
    .filter-actions { display:flex; gap:10px; align-items:center; }
    .btn-secondary { padding: 12px 25px; border-radius: 12px; font-weight: 800; font-size: 0.875rem; display: inline-flex; align-items: center; justify-content: center; gap: 10px; cursor: pointer; transition: all 0.3s; text-decoration: none; color: var(--text-dim); border:none; background:transparent;}
    .btn-secondary:hover { color: var(--brand-orange); background: var(--hover-bg); }

    .status-pill { display: inline-flex; align-items: center; gap: 6px; padding: 8px 18px; border-radius: 15px; font-size: 11px; font-weight: 900; text-transform: uppercase; letter-spacing: 0.5px; }
    .status-approved { background: rgba(16, 185, 129, 0.1); color: var(--brand-emerald); }
    .status-rejected { background: rgba(239, 68, 68, 0.1); color: #ef4444; }
    .status-pending { background: rgba(245, 158, 11, 0.1); color: #f59e0b; }

    .alert-premium { display: flex; align-items: center; gap: 15px; padding: 20px 30px; border-radius: 20px; font-weight: 700; background: rgba(16, 185, 129, 0.1); border: 1px solid rgba(16, 185, 129, 0.3); color: var(--brand-emerald); }
    .alert-icon-wrapper { width: 36px; height: 36px; border-radius: 12px; display: flex; align-items: center; justify-content: center; background: rgba(255,255,255,0.2); }

    .shadow-premium { box-shadow: 0 10px 30px var(--shadow-color); }
    .shadow-blue-light { box-shadow: 0 10px 20px rgba(59, 130, 246, 0.15); }
    .shadow-emerald-light { box-shadow: 0 10px 20px rgba(16, 185, 129, 0.15); }
    .shadow-orange-light { box-shadow: 0 10px 20px rgba(249, 115, 22, 0.15); }

    .empty-state { text-align: center; padding: 100px 40px !important; }
    .empty-icon-wrapper { display: flex; justify-content: center; margin-bottom: 20px; opacity: 0.5; }
    .empty-text { font-weight: 700; color: var(--text-dim); margin-top: 15px; }

    .table-footer { display: flex; justify-content: space-between; align-items: center; padding: 20px; border-top: 1px solid var(--card-border); flex-wrap: wrap; gap: 20px; }
    .per-page-footer { display: flex; align-items: center; gap: 10px; color: var(--text-secondary); font-weight: 600; font-size: 0.875rem; }
    .footer-select { padding: 8px 16px; background: var(--hover-bg); border: 1px solid var(--card-border); border-radius: 12px; color: var(--text-main); font-weight: 600; cursor: pointer; transition: all 0.3s; appearance: none; background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 24 24' stroke='%236b7280'%3E%3Cpath stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='M19 9l-7 7-7-7'%3E%3C/path%3E%3C/svg%3E"); background-repeat: no-repeat; background-position: right 12px center; background-size: 16px; padding-right: 40px; }
    .dark .footer-select { background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 24 24' stroke='%2394a3b8'%3E%3Cpath stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='M19 9l-7 7-7-7'%3E%3C/path%3E%3C/svg%3E"); }
    .footer-select:focus { outline: none; border-color: var(--brand-blue); background-color: var(--card-bg); }

    .pagination-wrapper { display: flex; justify-content: center; }

    @media (max-width: 900px) {
        .stats-grid { grid-template-columns: 1fr; }
        .stat-group { grid-template-columns: 1fr; }
        .page-title { font-size: 2.5rem; }
        .report-header { flex-direction:column; align-items:flex-start; }
        .btn-primary { width:100%; justify-content:center; }
    }
</style>

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
@endsection
