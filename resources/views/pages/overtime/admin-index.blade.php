@extends('layouts.app')
@section('title', 'Laporan Lembur (Admin)')

@section('content')
<div class="history-container animate-fade-in" x-data="{ 
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
    <div class="history-header">
        <div class="header-main">
            <h1 class="header-title">Laporan <span class="text-brand">Lembur</span></h1>
            <p class="header-subtitle">Panel verifikasi dan pengelolaan lembur seluruh karyawan.</p>
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
            <div class="stat-badge-pill">Status: Urgent</div>
        </div>

        <div class="stat-group">
            <div class="stat-card glass shadow-premium">
                <p class="stat-label text-dim">Total Bulan Ini</p>
                <div class="stat-item-row">
                    <div class="icon-box blue shadow-blue-light">
                        <svg class="h-8 w-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" stroke-width="2.5" stroke-linecap="round"/></svg>
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

    <div class="filter-card glass shadow-premium mb-8">
        <form action="{{ route('admin.overtime.index') }}" method="GET" class="filter-grid">
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
            <div class="filter-actions self-end">
                <button type="submit" class="btn-filter">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" stroke-width="3"/></svg>
                    <span>Cari</span>
                </button>
                @if(request()->anyFilled(['search', 'unit', 'status', 'start_date', 'end_date']))
                    <a href="{{ route('admin.overtime.index') }}" class="btn-reset">Reset</a>
                @endif
            </div>
        </form>
    </div>

    <div class="content-card glass shadow-premium">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table-custom">
                    <thead>
                        <tr>
                            <th>Karyawan</th>
                            <th>Tanggal Laporan</th>
                            <th>Waktu Tugas</th>
                            <th>Alasan</th>
                            <th>Status Akun</th>
                            <th class="text-right">Aksi Panel</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($overtimes as $ot)
                            <tr class="table-row-hover">
                                <td>
                                    <div class="user-info">
                                        <div class="user-avatar shadow-premium">
                                            {{ strtoupper(substr($ot->user_name, 0, 1)) }}
                                        </div>
                                        <div>
                                            <p class="user-name">{{ $ot->user_name }}</p>
                                            <p class="user-nip">{{ $ot->user_nip }} • {{ $ot->user_unit }}</p>
                                        </div>
                                    </div>
                                </td>
                                <td class="font-bold text-main">
                                    {{ \Carbon\Carbon::parse($ot->date)->isoFormat('D MMM YYYY') }}
                                </td>
                                <td>
                                    <div class="time-range-display">
                                        <span class="time-pill-in">{{ substr($ot->start_time, 0, 5) }}</span>
                                        <svg class="w-4 h-4 text-dim" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M9 5l7 7-7 7" stroke-width="3"/></svg>
                                        <span class="time-pill-out">{{ substr($ot->end_time, 0, 5) }}</span>
                                    </div>
                                </td>
                                <td>
                                    <div class="truncate-2-lines max-w-xs text-secondary-custom">
                                        {{ $ot->reason }}
                                    </div>
                                </td>
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
                                <td class="text-right">
                                    @if($ot->status === 'pending')
                                        <div class="flex justify-end gap-2">
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
                                        <span class="text-dim text-xs font-bold italic">Selesai</span>
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
        </div>
    </div>

    <div class="pagination-container mt-6">
        {{ $overtimes->links() }}
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

    .history-container { max-width: 1300px; margin: 0 auto; padding: 60px 20px 40px; font-family: 'Outfit', sans-serif; }
    .history-header { display: flex; justify-content: space-between; align-items: flex-end; gap: 30px; margin-bottom: 60px; flex-wrap: wrap; }
    .header-main { flex: 1; }
    .header-title { font-size: clamp(2rem, 5vw, 3.5rem); font-weight: 900; letter-spacing: -2px; line-height: 1; color: var(--text-main); }
    .text-brand { color: var(--brand-blue); }
    .header-subtitle { color: var(--text-dim); font-size: 1rem; margin-top: 10px; }

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
    .icon-box.blue { background: rgba(59, 130, 246, 0.1); color: var(--brand-blue); }
    .icon-box.emerald { background: rgba(16, 185, 129, 0.1); color: var(--brand-emerald); }
    .stat-time { font-size: 2.5rem; font-weight: 900; letter-spacing: -1.5px; color: var(--text-main); }
    .stat-date { color: var(--text-dim); font-size: 0.875rem; margin-top: 4px; }
    .stat-badge-pill { display: inline-block; padding: 4px 12px; background: rgba(255,255,255,0.2); border-radius: 20px; font-size: 0.75rem; font-weight: 600; margin-top: 10px; }

    /* Filter Card */
    .filter-card { padding: 30px; border-radius: 30px; background: var(--card-bg); border: 1px solid var(--card-border); }
    .filter-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 20px; }
    .filter-label { font-size: 9px; font-weight: 900; text-transform: uppercase; letter-spacing: 1px; color: var(--text-dim); margin-bottom: 8px; display: block; }
    .filter-input { width: 100%; padding: 12px 18px; border-radius: 12px; background: var(--hover-bg); border: 1px solid var(--card-border); color: var(--text-main); font-weight: 600; font-size: 0.875rem; transition: all 0.3s; }
    .filter-input:focus { outline: none; border-color: var(--brand-blue); background: var(--card-bg); }
    .btn-filter, .btn-reset { padding: 12px 25px; border-radius: 12px; font-weight: 800; font-size: 0.875rem; display: inline-flex; align-items: center; justify-content: center; gap: 10px; cursor: pointer; transition: all 0.3s; }
    .btn-filter { background: var(--brand-blue); color: white; border: none; }
    .btn-filter:hover { transform: translateY(-3px); box-shadow: 0 10px 20px rgba(59,130,246,0.2); }
    .btn-reset { color: var(--text-dim); }

    /* Table */
    .content-card { border-radius: 35px; overflow: hidden; background: var(--card-bg); border: 1px solid var(--card-border); }
    .table-custom { width: 100%; border-collapse: separate; border-spacing: 0; }
    .table-custom th { background: var(--hover-bg); padding: 22px 30px; text-align: left; font-size: 10px; font-weight: 900; text-transform: uppercase; letter-spacing: 2px; color: var(--text-dim); border-bottom: 1px solid var(--card-border); }
    .table-custom td { padding: 22px 30px; border-bottom: 1px solid var(--card-border); transition: all 0.2s; vertical-align: middle; color: var(--text-main); }
    .table-row-hover:hover td { background: var(--hover-bg); }

    /* User Cell */
    .user-info { display: flex; align-items: center; gap: 15px; }
    .user-avatar { width: 45px; height: 45px; border-radius: 15px; background: var(--brand-blue); color: white; display: flex; align-items: center; justify-content: center; font-weight: 900; font-size: 1.2rem; }
    .user-name { font-weight: 800; color: var(--text-main); font-size: 1rem; }
    .user-nip { font-size: 0.75rem; color: var(--text-dim); font-weight: 600; }

    .time-range-display { display: flex; align-items: center; gap: 8px; }
    .time-pill-in, .time-pill-out { padding: 4px 12px; border-radius: 10px; font-size: 0.75rem; font-weight: 800; }
    .time-pill-in { background: rgba(59,130,246,0.1); color: var(--brand-blue); }
    .time-pill-out { background: rgba(249,115,22,0.1); color: var(--brand-orange); }

    .status-pill { display: inline-flex; align-items: center; padding: 6px 14px; border-radius: 12px; font-size: 9px; font-weight: 900; text-transform: uppercase; letter-spacing: 0.5px; }
    .status-approved { background: rgba(16,185,129,0.1); color: var(--brand-emerald); }
    .status-rejected { background: rgba(239,68,68,0.1); color: #ef4444; }
    .status-pending { background: rgba(245,158,11,0.1); color: #f59e0b; }

    .btn-icon-action { width: 40px; height: 40px; border-radius: 12px; display: flex; align-items: center; justify-content: center; cursor: pointer; transition: all 0.2s; border: 1px solid var(--card-border); background: var(--hover-bg); }
    .btn-approve { color: var(--brand-emerald); }
    .btn-approve:hover { background: var(--brand-emerald); color: white; transform: scale(1.1); }
    .btn-reject { color: #ef4444; }
    .btn-reject:hover { background: #ef4444; color: white; transform: scale(1.1); }

    /* Modal */
    .modal-overlay { position: fixed; inset: 0; background: rgba(0,0,0,0.85); backdrop-filter: blur(10px); display: flex; align-items: center; justify-content: center; z-index: 9999; padding: 20px; }
    .modal-content-glass { background: var(--card-bg); width: 100%; max-width: 450px; border-radius: 35px; padding: 40px; border: 1px solid var(--card-border); box-shadow: 0 25px 50px rgba(0,0,0,0.5); }
    .modal-textarea { width: 100%; padding: 20px; border-radius: 20px; background: var(--hover-bg); border: 1px solid var(--card-border); color: var(--text-main); font-family: inherit; font-size: 0.9rem; font-weight: 600; resize: none; }
    .modal-textarea:focus { outline: none; border-color: var(--brand-blue); }
    .btn-modal-cancel, .btn-modal-confirm { flex: 1; padding: 15px; border-radius: 18px; font-weight: 800; text-transform: uppercase; letter-spacing: 1px; font-size: 0.8rem; cursor: pointer; transition: all 0.3s; display: flex; align-items: center; justify-content: center; border: none; }
    .empty-state { text-align: center; padding: 100px 40px !important; }
    .empty-icon-wrapper { display: flex; justify-content: center; margin-bottom: 20px; opacity: 0.5; }
    .empty-text { font-weight: 700; color: var(--text-dim); margin-top: 15px; }
    .btn-modal-confirm { color: white; }
    .btn-modal-confirm:hover { transform: translateY(-3px); }

    .shadow-premium { box-shadow: 0 10px 30px rgba(0,0,0,0.05); }
    .bg-emerald { background: var(--brand-emerald); }
    .shadow-blue-light { box-shadow: 0 10px 20px rgba(59, 130, 246, 0.1); }
    .shadow-emerald-light { box-shadow: 0 10px 20px rgba(16, 185, 129, 0.1); }
    .shadow-red-light { box-shadow: 0 10px 20px rgba(239, 68, 68, 0.1); }
    .pulse { animation: pulse 2s infinite; }
    @keyframes pulse { 0% { opacity: 1; } 50% { opacity: 0.6; } 100% { opacity: 1; } }

    /* Pagination Styles */
    .pagination-container nav {
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        gap: 16px;
        width: 100%;
        margin-top: 30px;
    }

    @media (min-width: 768px) {
        .pagination-container nav {
            flex-direction: row;
            justify-content: space-between;
        }
    }

    .pagination-container .text-sm {
        color: var(--text-dim) !important;
    }

    .pagination-container .font-medium {
        color: var(--text-main) !important;
    }

    .dark .pagination-container span,
    .dark .pagination-container p {
        color: var(--text-dim) !important;
    }

    .dark .pagination-container nav a {
        background-color: var(--card-bg) !important;
        border-color: var(--card-border) !important;
        color: var(--text-dim) !important;
    }

    .dark .pagination-container nav a:hover {
        background-color: var(--hover-bg) !important;
        color: var(--text-main) !important;
    }

    .dark .pagination-container nav span[aria-current="page"] > span {
        background-color: var(--brand-blue) !important;
        border-color: var(--brand-blue) !important;
        color: white !important;
    }

    @media (min-width: 640px) {
        .pagination-container nav > div:first-child { 
            display: none !important; 
        }
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
@endsection
