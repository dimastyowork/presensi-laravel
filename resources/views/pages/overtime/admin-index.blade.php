@extends('layouts.app')
@section('title', 'Laporan Lembur (Admin)')

@section('content')
<div class="hrd-report-container animate-fade-in" x-data="{ 
    showActionModal: false, 
    actionType: 'approve', 
    targetId: null,
    adminNote: '',
    targetName: '',
    
    openModal(type, id, name) {
        this.actionType = type;
        this.targetId = id;
        this.targetName = name;
        this.adminNote = '';
        this.showActionModal = true;
    }
}">
    <div class="report-header">
        <div class="header-main">
            <h1 class="page-title">Laporan <span class="text-brand">Lembur</span></h1>
            <p class="page-subtitle">Panel verifikasi dan pengelolaan lembur seluruh karyawan.</p>
        </div>
    </div>

    <div class="stats-grid">
        <div class="stat-card card-gradient shadow-premium">
            <div class="card-blur-circle"></div>
            <p class="stat-label">Perlu Verifikasi</p>
            <div class="stat-main">
                 <h3 class="stat-value">{{ $stats['pending'] }}</h3>
                 <span class="stat-unit">pengajuan baru</span>
            </div>
        </div>

        <div class="stat-group">
            <div class="stat-card glass shadow-premium">
                <p class="stat-label text-dim">Total Bulan Ini</p>
                <div class="stat-item-row">
                    <div class="icon-box orange shadow-orange-light">
                        <svg class="h-8 w-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" stroke-width="2.5" stroke-linecap="round"/></svg>
                    </div>
                    <div>
                        <h4 class="stat-time text-main">{{ $stats['total_month'] }}</h4>
                        <p class="stat-date text-dim">Total Pengajuan Diterima</p>
                    </div>
                </div>
            </div>

            <div class="stat-card glass shadow-premium">
                <p class="stat-label text-dim">Disetujui Hari Ini</p>
                <div class="stat-item-row">
                    <div class="icon-box emerald shadow-emerald-light">
                        <svg class="h-8 w-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M5 13l4 4L19 7" stroke-width="2.5" stroke-linecap="round"/></svg>
                    </div>
                    <div>
                        <h4 class="stat-time text-main">{{ $stats['approved_today'] }}</h4>
                        <p class="stat-date text-dim">Validasi Berhasil Selesai</p>
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

    <div class="filter-section glass shadow-premium mb-8">
        <form action="{{ route('admin.overtime.index') }}" method="GET" class="filter-form">
            <div class="filter-grid">
                <div class="filter-group">
                    <label class="filter-label">Karyawan / NIP</label>
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama atau NIP..." class="filter-input">
                </div>
                <div class="filter-group">
                    <label class="filter-label">Unit</label>
                    <select name="unit" class="filter-input">
                        <option value="">Semua Unit</option>
                        @foreach($units as $u)
                            <option value="{{ $u }}" {{ request('unit') == $u ? 'selected' : '' }}>{{ $u }}</option>
                        @endforeach
                    </select>
                </div>
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
                    <span>Cari Pengajuan</span>
                </button>
                @if(request()->anyFilled(['search', 'unit', 'status', 'start_date', 'end_date']))
                    <a href="{{ route('admin.overtime.index') }}" class="btn-secondary">Reset</a>
                @endif
            </div>
        </form>
    </div>

    <div class="table-container glass shadow-premium">
        <div class="card-body p-0">
            <div class="table-wrapper">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th style="width: 100px;">Aksi</th>
                            <th>Karyawan</th>
                            <th>Tanggal</th>
                            <th>Waktu Tugas</th>
                            <th>Alasan Lembur</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($overtimes as $ot)
                            <tr>
                                {{-- Aksi Panel --}}
                                <td>
                                    @if($ot->status === 'pending')
                                        <div class="flex gap-2">
                                            <button @click="openModal('approve', {{ $ot->id }}, '{{ addslashes($ot->user_name) }}')" 
                                                    class="btn-icon-action btn-approve" title="Setujui">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M5 13l4 4L19 7" stroke-width="3" stroke-linecap="round"/></svg>
                                            </button>
                                            <button @click="openModal('reject', {{ $ot->id }}, '{{ addslashes($ot->user_name) }}')" 
                                                    class="btn-icon-action btn-reject" title="Tolak">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M6 18L18 6M6 6l12 12" stroke-width="3" stroke-linecap="round"/></svg>
                                            </button>
                                        </div>
                                    @else
                                        <span class="text-dim text-[10px] font-black italic uppercase">Finished</span>
                                    @endif
                                </td>

                                {{-- Karyawan --}}
                                <td>
                                    <div class="flex flex-col gap-1">
                                        <span class="font-bold text-main">{{ $ot->user_name }}</span>
                                        <div class="flex items-center gap-1.5">
                                            <span class="px-1.5 py-0.5 rounded bg-slate-100 dark:bg-slate-800 text-[10px] font-bold text-secondary border border-slate-200 dark:border-slate-700">
                                                {{ $ot->user_nip }}
                                            </span>
                                            <span class="px-1.5 py-0.5 rounded bg-blue-50 dark:bg-blue-900/30 text-[10px] font-bold text-blue-600 dark:text-blue-400 border border-blue-100 dark:border-blue-800">
                                                {{ $ot->user_unit }}
                                            </span>
                                        </div>
                                    </div>
                                </td>

                                {{-- Tanggal --}}
                                <td class="font-bold text-main">
                                    {{ \Carbon\Carbon::parse($ot->date)->isoFormat('D MMM YYYY') }}
                                </td>

                                {{-- Waktu --}}
                                <td>
                                    <div class="attendance-stack">
                                        <div class="stack-item in">
                                            <svg class="w-2.5 h-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M11 16l-4-4m0 0l4-4m-4 4h14" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"/></svg>
                                            <span>{{ substr($ot->start_time, 0, 5) }}</span>
                                        </div>
                                        <div class="stack-item out">
                                            <svg class="w-2.5 h-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M17 16l4-4m0 0l-4-4m4 4H7" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"/></svg>
                                            <span>{{ substr($ot->end_time, 0, 5) }}</span>
                                        </div>
                                    </div>
                                </td>

                                {{-- Alasan --}}
                                <td>
                                    <div class="truncate-2-lines max-w-xs text-sm text-secondary">
                                        {{ $ot->reason }}
                                    </div>
                                </td>

                                {{-- Status --}}
                                <td>
                                    @if($ot->status === 'approved')
                                        <span class="status-pill status-approved">Disetujui</span>
                                        <p class="text-[9px] mt-1 text-dim">Oleh: {{ $ot->approved_by }}</p>
                                    @elseif($ot->status === 'rejected')
                                        <span class="status-pill status-rejected">Ditolak</span>
                                        <p class="text-[9px] mt-1 text-dim">Oleh: {{ $ot->approved_by }}</p>
                                    @else
                                        <span class="status-pill status-pending pulse">Pending</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="empty-state">
                                    <div class="empty-icon-wrapper">
                                        <svg class="w-16 h-16 text-dim opacity-20" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" stroke-width="1.5"/></svg>
                                    </div>
                                    <p class="empty-text">Tidak ada data lembur ditemukan</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="table-footer glass">
                <div class="per-page-footer">
                    <form action="{{ route('admin.overtime.index') }}" method="GET" id="perPageForm">
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

    <div x-show="showActionModal" class="modal-overlay" @click="showActionModal = false" x-cloak x-transition.opacity>
        <div class="modal-content-glass shadow-high" @click.stop x-transition.scale>
            <div class="modal-header-custom mb-6">
                <h3 class="text-2xl font-black mb-2" :class="actionType === 'approve' ? 'text-emerald' : 'text-red-500'">
                    Konfirmasi <span x-text="actionType === 'approve' ? 'Persetujuan' : 'Penolakan'"></span>
                </h3>
                <p class="text-dim">Pengajuan lembur untuk <span class="font-bold text-main" x-text="targetName"></span></p>
            </div>

            <form :action="actionType === 'approve' ? '/admin/overtime/approve/' + targetId : '/admin/overtime/reject/' + targetId" method="POST">
                @csrf
                <div class="mb-6">
                    <label class="field-label-mini mb-2 block">Catatan Admin (Opsional)</label>
                    <textarea name="admin_note" rows="3" class="modal-textarea" x-model="adminNote" placeholder="Tulis catatan verifikasi di sini..."></textarea>
                </div>

                <div class="flex gap-3">
                    <button type="button" @click="showActionModal = false" class="btn-modal-cancel">Batal</button>
                    <button type="submit" class="btn-modal-confirm" :class="actionType === 'approve' ? 'bg-emerald shadow-emerald-light' : 'bg-red-500 shadow-red-light'">
                        Konfirmasi <span x-text="actionType === 'approve' ? 'Setujui' : 'Tolak'"></span>
                    </button>
                </div>
            </form>
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
        
        --text-main: #1e293b;
        --text-secondary: #64748b;
        --text-dim: #94a3b8;
        --card-bg: #ffffff;
        --card-border: rgba(0, 0, 0, 0.08);
        --glass-bg: rgba(255, 255, 255, 0.8);
        --hover-bg: rgba(0, 0, 0, 0.03);
        --header-bg: #f8fafc;
        --shadow-color: rgba(0, 0, 0, 0.05);
        --brand-blue-hover: #1d4ed8;
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
        --brand-blue-hover: #3b82f6;
        --shadow-color: rgba(0, 0, 0, 0.3);
    }

    .glass {
        background: var(--glass-bg);
        border: 1px solid var(--card-border);
        backdrop-filter: blur(20px);
        -webkit-backdrop-filter: blur(20px);
    }

    .hrd-report-container { max-width: 1400px; margin: 0 auto; padding: 20px 20px 40px; font-family: 'Outfit', sans-serif; }
    
    .report-header { display: flex; justify-content: space-between; align-items: flex-end; gap: 30px; margin-bottom: 40px; }
    .page-title { font-size: clamp(2rem, 5vw, 3.5rem); font-weight: 900; letter-spacing: -2px; line-height: 1; margin: 0; color: var(--text-main); }
    .page-subtitle { color: var(--text-dim); font-size: 1rem; margin-top: 10px; font-weight: 500; }
    .text-brand { color: var(--brand-blue); }

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
    .icon-box { width: 70px; height: 70px; border-radius: 25px; display: flex; align-items: center; justify-content: center; }
    .icon-box.orange { background: rgba(249, 115, 22, 0.1); color: var(--brand-orange); }
    .icon-box.emerald { background: rgba(16, 185, 129, 0.1); color: var(--brand-emerald); }

    .shadow-premium { box-shadow: 0 10px 30px rgba(0,0,0,0.05); }
    .shadow-blue-light { box-shadow: 0 10px 20px rgba(59, 130, 246, 0.1); }
    .shadow-emerald-light { box-shadow: 0 10px 20px rgba(16, 185, 129, 0.1); }
    .shadow-orange-light { box-shadow: 0 10px 20px rgba(249, 115, 22, 0.1); }

    .stat-item-row { display: flex; align-items: center; gap: 15px; margin-top: 10px; }
    .stat-time { font-size: 2.5rem; font-weight: 900; letter-spacing: -1.5px; color: var(--text-main); }
    .stat-date { color: var(--text-dim); font-size: 0.875rem; margin-top: 4px; }

    /* Filter Section - Standardized */
    .filter-section { background: var(--glass-bg); border: 1px solid var(--card-border); border-radius: 24px; padding: 24px; backdrop-filter: blur(20px); margin-bottom: 30px;}
    .filter-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 20px; }
    .filter-label { font-size: 0.75rem; font-weight: 800; text-transform: uppercase; letter-spacing: 1px; color: var(--text-dim); margin-bottom: 8px; display: block; }
    .filter-input { width: 100%; padding: 12px 16px; border-radius: 12px; background: var(--hover-bg); border: 1px solid var(--card-border); color: var(--text-main); font-weight: 600; font-size: 0.9rem; transition: all 0.3s; }
    .filter-input:focus { outline: none; border-color: var(--brand-blue); background: var(--card-bg); }
    .filter-actions { display:flex; gap:12px; margin-top:20px; align-items:center;}
    .btn-secondary { color: var(--text-dim); font-weight: 700; text-decoration:none; padding: 12px 20px; border-radius:12px; transition: all 0.2s;}
    .btn-secondary:hover { background: var(--hover-bg); color: var(--brand-orange);}
    .btn-primary { background: var(--brand-blue); color:white; border:none; padding: 12px 24px; border-radius:12px; font-weight:800; display:flex; align-items:center; gap:8px; cursor:pointer; }
    .btn-primary:hover { background: var(--brand-blue-hover); transform: translateY(-2px); }

    /* Table - Standardized */
    .table-container { border-radius: 35px; overflow: hidden; background: var(--card-bg); border: 1px solid var(--card-border); }
    .table-wrapper { width: 100%; overflow-x: auto; }
    .data-table { width: 100%; border-collapse: separate; border-spacing: 0; min-width: 800px; }
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
    .data-table td { padding: 22px 30px; border-bottom: 1px solid var(--card-border); vertical-align: middle; color: var(--text-main); }

    /* Row Hover Effect */
    .data-table tbody tr { transition: all 0.2s ease; }
    .data-table tbody tr:hover td { background: var(--hover-bg) !important; }

    /* Action Buttons */
    .btn-icon-action { width: 36px; height: 36px; border-radius: 10px; display: flex; align-items: center; justify-content: center; transition: all 0.2s; border: 1px solid var(--card-border); background: var(--hover-bg); }
    .btn-approve { color: var(--brand-green); }
    .btn-approve:hover { background: var(--brand-green); color: white; transform: translateY(-2px); }
    .btn-reject { color: var(--brand-red); }
    .btn-reject:hover { background: var(--brand-red); color: white; transform: translateY(-2px); }

    /* Status Pills */
    .status-pill { display: inline-flex; align-items: center; padding: 6px 14px; border-radius: 10px; font-size: 10px; font-weight: 900; text-transform: uppercase; letter-spacing: 0.5px; }
    .status-approved { background: rgba(16, 185, 129, 0.1); color: var(--brand-green); }
    .status-rejected { background: rgba(239, 68, 68, 0.1); color: var(--brand-red); }
    .status-pending { background: rgba(245, 158, 11, 0.1); color: #f59e0b; }

    /* Attendance Stack */
    .attendance-stack { display: flex; flex-direction: column; gap: 4px; }
    .stack-item { display: flex; align-items: center; gap: 6px; font-size: 0.8rem; font-weight: 700; padding: 2px 8px; border-radius: 6px; width: fit-content; }
    .stack-item.in { background: rgba(37, 99, 235, 0.08); color: var(--brand-blue); }
    .stack-item.out { background: rgba(249, 115, 22, 0.08); color: var(--brand-orange); }

    /* Modal */
    .modal-overlay { position: fixed; inset: 0; background: rgba(0,0,0,0.8); backdrop-filter: blur(8px); display: flex; align-items: center; justify-content: center; z-index: 9999; padding: 20px; }
    .modal-content-glass { background: var(--card-bg); width: 100%; max-width: 450px; border-radius: 30px; padding: 40px; border: 1px solid var(--card-border); box-shadow: 0 25px 50px rgba(0,0,0,0.3); }
    .modal-textarea { width: 100%; padding: 16px; border-radius: 15px; background: var(--hover-bg); border: 1px solid var(--card-border); color: var(--text-main); font-family: inherit; font-size: 0.9rem; font-weight: 600; resize: none; }
    .btn-modal-cancel, .btn-modal-confirm { flex: 1; padding: 12px; border-radius: 12px; font-weight: 800; text-transform: uppercase; font-size: 0.8rem; cursor: pointer; transition: all 0.3s; border: none; }
    .btn-modal-confirm { color: white; }

    /* Empty state */
    .empty-state { text-align: center; padding: 80px 40px !important; }
    .empty-icon-wrapper { display: flex; justify-content: center; margin-bottom: 20px; opacity: 0.3; }
    .empty-text { font-weight: 800; color: var(--text-dim); text-transform: uppercase; letter-spacing: 1px; }

    /* Footer */
    .table-footer { display: flex; justify-content: space-between; align-items: center; padding: 20px 24px; border-top: 1px solid var(--card-border); }
    .footer-select { padding: 8px 32px 8px 16px; background: var(--hover-bg); border: 1px solid var(--card-border); border-radius: 10px; color: var(--text-main); font-weight: 700; cursor: pointer; appearance: none; background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 24 24' stroke='%236b7280'%3E%3Cpath stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='M19 9l-7 7-7-7'%3E%3C/path%3E%3C/svg%3E"); background-repeat: no-repeat; background-position: right 12px center; background-size: 14px; }

    .dark .footer-select {
        background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 24 24' stroke='%2394a3b8'%3E%3Cpath stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='M19 9l-7 7-7-7'%3E%3C/path%3E%3C/svg%3E");
    }

    .footer-select:focus {
        outline: none;
        border-color: var(--brand-blue);
        background-color: var(--card-bg);
    }

    /* Pagination Styles */
    .pagination-wrapper {
        display: flex;
        justify-content: center;
    }

    @media (max-width: 1024px) {
        .stats-grid { grid-template-columns: 1fr; }
        .stat-group { grid-template-columns: 1fr; }
        .filter-grid { grid-template-columns: 1fr 1fr; }
    }
    @media (max-width: 600px) {
        .filter-grid { grid-template-columns: 1fr; }
        .user-info { flex-direction: column; align-items: flex-start; gap: 5px; }
        .user-avatar { display: none; }
        .header-title { font-size: 2.25rem; }
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
