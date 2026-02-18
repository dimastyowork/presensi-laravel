@extends('layouts.app')
@section('title', 'Riwayat Lembur')

@section('content')
<div class="history-container animate-fade-in">
    <!-- Header Content -->
    <div class="history-header">
        <div class="header-main">
            <h1 class="header-title">Pengajuan <span class="text-brand">Lembur</span></h1>
            <p class="header-subtitle">Pantau status dan riwayat pengajuan lembur Anda.</p>
        </div>
        <div class="header-action">
            <a href="{{ route('overtime.create') }}" class="btn-primary-custom shadow-premium">
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
    <div class="filter-card glass shadow-premium mb-8">
        <form action="{{ route('overtime.index') }}" method="GET" class="filter-grid">
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
            <div class="filter-actions self-end">
                <button type="submit" class="btn-filter">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" stroke-width="3"/></svg>
                    <span>Filter</span>
                </button>
                @if(request()->anyFilled(['status', 'start_date', 'end_date']))
                    <a href="{{ route('overtime.index') }}" class="btn-reset">Reset</a>
                @endif
            </div>
        </form>
    </div>

    <!-- Content Table -->
    <div class="content-card glass shadow-premium">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table-custom">
                    <thead>
                        <tr>
                            <th>Tanggal</th>
                            <th>Waktu</th>
                            <th>Durasi</th>
                            <th>Alasan</th>
                            <th>Status</th>
                            <th>Admin Note</th>
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
                            <tr class="table-row-hover">
                                <td class="font-bold text-main">
                                    {{ \Carbon\Carbon::parse($ot->date)->isoFormat('dddd, D MMMM YYYY') }}
                                </td>
                                <td>
                                    <div class="time-range-display">
                                        <span class="time-pill-in">{{ substr($ot->start_time, 0, 5) }}</span>
                                        <svg class="w-4 h-4 text-dim" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M9 5l7 7-7 7" stroke-width="3"/></svg>
                                        <span class="time-pill-out">{{ substr($ot->end_time, 0, 5) }}</span>
                                    </div>
                                </td>
                                <td>
                                    <span class="duration-badge">
                                        {{ $hours }} jam {{ $mins > 0 ? $mins . ' mnt' : '' }}
                                    </span>
                                </td>
                                <td class="max-w-xs text-secondary-custom truncate-2-lines">
                                    {{ $ot->reason }}
                                </td>
                                <td>
                                    @if($ot->status === 'approved')
                                        <span class="status-pill status-approved">
                                            <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20"><path d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"/></svg>
                                            Disetujui
                                        </span>
                                    @elseif($ot->status === 'rejected')
                                        <span class="status-pill status-rejected">
                                            <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20"><path d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z"/></svg>
                                            Ditolak
                                        </span>
                                    @else
                                        <span class="status-pill status-pending text-xs">
                                            <svg class="w-3 h-3 animate-spin" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" stroke-width="3" stroke-linecap="round"/></svg>
                                            Menunggu
                                        </span>
                                    @endif
                                </td>
                                <td class="text-xs text-dim italic">
                                    {{ $ot->admin_note ?? '-' }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="empty-state">
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
        </div>
    </div>

    <!-- Pagination -->
    <div class="pagination-container mt-6">
        {{ $overtimes->links() }}
    </div>
</div>

<style>
    :root {
        --brand-blue: #3b82f6;
        --brand-blue-hover: #2563eb;
        --brand-orange: #f97316;
        --brand-emerald: #10b981;
        --text-main: #1e293b;
        --text-dim: #94a3b8;
        --card-bg: #ffffff;
        --card-border: rgba(0, 0, 0, 0.05);
        --hover-bg: rgba(0, 0, 0, 0.02);
    }

    .dark {
        --text-main: #f8fafc;
        --text-dim: #64748b;
        --card-bg: #1f2937;
        --card-border: rgba(255, 255, 255, 0.08);
        --hover-bg: rgba(255, 255, 255, 0.05);
    }

    .history-container { max-width: 1200px; margin: 0 auto; padding: 60px 20px 40px; font-family: 'Outfit', sans-serif; }
    .history-header { display: flex; justify-content: space-between; align-items: flex-end; gap: 30px; margin-bottom: 60px; flex-wrap: wrap; }
    .header-main { flex: 1; }
    .header-title { font-size: clamp(2rem, 5vw, 3.5rem); font-weight: 900; letter-spacing: -2px; line-height: 1; color: var(--text-main); }
    .text-brand { color: var(--brand-blue); }
    .header-subtitle { color: var(--text-dim); font-size: 1rem; margin-top: 10px; }

    .btn-primary-custom { display: flex; align-items: center; gap: 10px; padding: 14px 28px; border-radius: 18px; background: var(--brand-blue); color: white; font-weight: 800; text-transform: uppercase; font-size: 0.875rem; letter-spacing: 1px; transition: all 0.3s cubic-bezier(0.175, 0.885, 0.32, 1.275); }
    .btn-primary-custom:hover { transform: translateY(-5px) scale(1.02); box-shadow: 0 15px 30px rgba(59, 130, 246, 0.3); background: var(--brand-blue-hover); }

    .stats-grid { display: grid; grid-template-columns: 1.2fr 1.8fr; gap: 30px; margin-bottom: 60px; }
    .stat-card { border-radius: 40px; padding: 40px; position: relative; overflow: hidden; background: var(--card-bg); border: 1px solid var(--card-border); transition: all 0.3s ease; }
    .card-gradient { background: linear-gradient(135deg, var(--brand-blue), var(--brand-blue-hover)); color: white; border: none; }
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

    .content-card { border-radius: 35px; overflow: hidden; background: var(--card-bg); border: 1px solid var(--card-border); }
    .table-custom { width: 100%; border-collapse: separate; border-spacing: 0; }
    .table-custom th { background: var(--hover-bg); padding: 22px 30px; text-align: left; font-size: 10px; font-weight: 900; text-transform: uppercase; letter-spacing: 2px; color: var(--text-dim); border-bottom: 1px solid var(--card-border); }
    .table-custom td { padding: 22px 30px; border-bottom: 1px solid var(--card-border); transition: all 0.2s; vertical-align: middle; color: var(--text-main); }
    .table-row-hover:hover td { background: var(--hover-bg); }

    .time-range-display { display: flex; align-items: center; gap: 8px; }
    .time-pill-in, .time-pill-out { padding: 4px 12px; border-radius: 10px; font-size: 0.75rem; font-weight: 800; }
    .time-pill-in { background: rgba(59,130,246,0.1); color: var(--brand-blue); }
    .time-pill-out { background: rgba(249,115,22,0.1); color: var(--brand-orange); }
    .duration-badge { font-size: 0.75rem; font-weight: 700; color: var(--text-dim); background: var(--hover-bg); padding: 6px 14px; border-radius: 12px; }

    .status-pill { display: inline-flex; align-items: center; gap: 6px; padding: 8px 18px; border-radius: 15px; font-size: 11px; font-weight: 900; text-transform: uppercase; letter-spacing: 0.5px; }
    .status-approved { background: rgba(16, 185, 129, 0.1); color: var(--brand-emerald); }
    .status-rejected { background: rgba(239, 68, 68, 0.1); color: #ef4444; }
    .status-pending { background: rgba(245, 158, 11, 0.1); color: #f59e0b; }

    .alert-premium { display: flex; align-items: center; gap: 15px; padding: 20px 30px; border-radius: 20px; font-weight: 700; background: rgba(16, 185, 129, 0.1); border: 1px solid rgba(16, 185, 129, 0.3); color: var(--brand-emerald); }
    .alert-icon-wrapper { width: 36px; height: 36px; border-radius: 12px; display: flex; align-items: center; justify-content: center; background: rgba(255,255,255,0.2); }

    .shadow-premium { box-shadow: 0 10px 30px rgba(0,0,0,0.05); }
    .shadow-emerald-light { box-shadow: 0 10px 20px rgba(16, 185, 129, 0.1); }
    .shadow-orange-light { box-shadow: 0 10px 20px rgba(249, 115, 22, 0.1); }

    .empty-state { text-align: center; padding: 100px 40px !important; }
    .empty-icon-wrapper { display: flex; justify-content: center; margin-bottom: 20px; opacity: 0.5; }
    .empty-text { font-weight: 700; color: var(--text-dim); margin-top: 15px; }

    /* Filter Styling */
    .filter-card { padding: 30px; border-radius: 30px; background: var(--card-bg); border: 1px solid var(--card-border); }
    .filter-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(180px, 1fr)); gap: 20px; }
    .filter-label { font-size: 9px; font-weight: 900; text-transform: uppercase; letter-spacing: 1px; color: var(--text-dim); margin-bottom: 8px; display: block; }
    .filter-input { width: 100%; padding: 12px 18px; border-radius: 12px; background: var(--hover-bg); border: 1px solid var(--card-border); color: var(--text-main); font-weight: 600; font-size: 0.875rem; transition: all 0.3s; }
    .filter-input:focus { outline: none; border-color: var(--brand-blue); background: var(--card-bg); }
    .btn-filter, .btn-reset { padding: 12px 25px; border-radius: 12px; font-weight: 800; font-size: 0.875rem; display: inline-flex; align-items: center; justify-content: center; gap: 10px; cursor: pointer; transition: all 0.3s; text-decoration: none; }
    .btn-filter { background: var(--brand-blue); color: white; border: none; }
    .btn-filter:hover { transform: translateY(-3px); box-shadow: 0 10px 20px rgba(59,130,246,0.2); }
    .btn-reset { color: var(--text-dim); }
    .btn-reset:hover { color: var(--brand-orange); background: var(--hover-bg); }

    @media (max-width: 900px) {
        .stats-grid { grid-template-columns: 1fr; }
        .stat-group { grid-template-columns: 1fr; }
        .header-title { font-size: 2.5rem; }
    }
</style>
@endsection
