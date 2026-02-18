@extends('layouts.app')
@section('title', 'Ajukan Lembur')

@section('content')
<div class="unit-form-container animate-fade-in">
    
    <!-- Header -->
    <div class="page-header">
        <div>
            <h1 class="page-title">Pengajuan <span class="text-brand">Lembur</span></h1>
            <p class="page-subtitle">Lengkapi formulir di bawah untuk mengajukan lembur</p>
        </div>
        <a href="{{ route('overtime.index') }}" class="btn-secondary shadow-premium">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
            </svg>
            Kembali
        </a>
    </div>

    <!-- Form -->
    <div class="form-card glass shadow-premium animate-slide-up">
        <form action="{{ route('overtime.store') }}" method="POST">
            @csrf
            
            <div class="form-grid">
                <!-- Date -->
                <div class="form-group full-width">
                    <label class="form-label">Tanggal Lembur <span class="required">*</span></label>
                    <input type="date" name="date" value="{{ old('date', date('Y-m-d')) }}" 
                        class="form-input @error('date') error @enderror" required>
                    @error('date')
                        <span class="error-message">{{ $message }}</span>
                    @enderror
                </div>

                <!-- Times -->
                <div class="form-group">
                    <label class="form-label">Waktu Mulai <span class="required">*</span></label>
                    <input type="time" name="start_time" value="{{ old('start_time') }}" 
                        class="form-input @error('start_time') error @enderror" required>
                    @error('start_time')
                        <span class="error-message">{{ $message }}</span>
                    @enderror
                </div>

                <div class="form-group">
                    <label class="form-label">Waktu Selesai <span class="required">*</span></label>
                    <input type="time" name="end_time" value="{{ old('end_time') }}" 
                        class="form-input @error('end_time') error @enderror" required>
                    @error('end_time')
                        <span class="error-message">{{ $message }}</span>
                    @enderror
                </div>

                <!-- Reason -->
                <div class="form-group full-width">
                    <label class="form-label">Alasan Lembur <span class="required">*</span></label>
                    <textarea name="reason" rows="4" class="form-input @error('reason') error @enderror" 
                        placeholder="Jelaskan detail pekerjaan lembur Anda..." required>{{ old('reason') }}</textarea>
                    @error('reason')
                        <span class="error-message">{{ $message }}</span>
                    @enderror
                </div>
            </div>

            <div class="form-actions">
                <button type="submit" class="btn-primary">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                    </svg>
                    Kirim Pengajuan
                </button>
                <a href="{{ route('overtime.index') }}" class="btn-cancel">Batal</a>
            </div>
        </form>
    </div>

    <!-- Info Tip -->
    <div class="stat-card glass shadow-premium mt-8 flex items-start gap-4" style="padding: 25px;">
        <div class="icon-box blue shadow-blue-light" style="width: 50px; height: 50px; border-radius: 15px;">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" stroke-width="2.5"/></svg>
        </div>
        <div>
            <h4 class="font-black text-brand uppercase tracking-widest" style="font-size: 11px;">Informasi</h4>
            <p class="text-sm font-medium text-dim leading-relaxed">Pastikan data waktu yang Anda isi sudah benar. Pengajuan lembur akan diverifikasi oleh Admin/HRD.</p>
        </div>
    </div>
</div>

<style>
    :root {
        --brand-blue: #3b82f6;
        --brand-blue-dark: #2563eb;
        --brand-red: #ef4444;
        --brand-green: #10b981;
        --text-main: #1e293b;
        --text-secondary: #64748b;
        --text-dim: #94a3b8;
        --card-bg: #ffffff;
        --card-border: rgba(0, 0, 0, 0.08);
        --input-bg: rgba(0, 0, 0, 0.02);
    }

    .dark {
        --text-main: #f8fafc;
        --text-secondary: #94a3b8;
        --text-dim: #64748b;
        --card-bg: #1f2937;
        --card-border: rgba(255, 255, 255, 0.08);
        --input-bg: rgba(255, 255, 255, 0.03);
    }

    .unit-form-container { max-width: 900px; margin: 0 auto; padding: 60px 20px 40px; font-family: 'Outfit', sans-serif; }
    .page-header { display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 40px; flex-wrap: wrap; gap: 20px; }
    .page-title { font-size: clamp(2rem, 4vw, 2.5rem); font-weight: 900; letter-spacing: -2px; color: var(--text-main); margin: 0; }
    .text-brand { color: var(--brand-blue); }
    .page-subtitle { color: var(--text-secondary); margin-top: 8px; font-size: 1rem; }

    .btn-secondary { display: flex; align-items: center; gap: 8px; padding: 12px 24px; background: var(--input-bg); color: var(--text-main); border: 1px solid var(--card-border); border-radius: 12px; font-weight: 700; text-decoration: none; transition: all 0.3s; }
    .btn-secondary:hover { background: var(--card-bg); border-color: var(--brand-blue); }

    .form-card { background: var(--card-bg); border: 1px solid var(--card-border); border-radius: 24px; padding: 40px; backdrop-filter: blur(20px); }
    .form-grid { display: grid; grid-template-columns: repeat(2, 1fr); gap: 24px; margin-bottom: 32px; }
    .form-group { display: flex; flex-direction: column; gap: 8px; }
    .full-width { grid-column: 1 / -1; }
    .form-label { font-size: 0.75rem; font-weight: 800; color: var(--text-secondary); text-transform: uppercase; letter-spacing: 1px; }
    .required { color: var(--brand-red); }

    .form-input { padding: 14px 20px; background: var(--input-bg); border: 1px solid var(--card-border); border-radius: 14px; color: var(--text-main); font-size: 1rem; font-weight: 600; transition: all 0.3s; font-family: inherit; }
    .form-input:focus { outline: none; border-color: var(--brand-blue); background: var(--card-bg); box-shadow: 0 0 0 4px rgba(59, 130, 246, 0.1); }
    .form-input.error { border-color: var(--brand-red); }

    .btn-primary { display: flex; align-items: center; gap: 8px; padding: 14px 28px; background: var(--brand-blue); color: white; border-radius: 12px; font-weight: 700; border: none; cursor: pointer; transition: all 0.3s; }
    .btn-primary:hover { background: var(--brand-blue-dark); transform: translateY(-2px); box-shadow: 0 10px 20px rgba(59, 130, 246, 0.3); }
    .btn-cancel { display: flex; align-items: center; padding: 14px 28px; background: var(--input-bg); color: var(--text-main); border: 1px solid var(--card-border); border-radius: 12px; font-weight: 700; text-decoration: none; transition: all 0.3s; }
    .btn-cancel:hover { background: var(--card-bg); border-color: var(--brand-red); color: var(--brand-red); }

    .form-actions { display: flex; gap: 12px; justify-content: flex-end; }
    .error-message { font-size: 0.8rem; color: var(--brand-red); font-weight: 700; }

    .shadow-premium { box-shadow: 0 10px 30px rgba(0,0,0,0.05); }
    .icon-box.blue { background: rgba(59, 130, 246, 0.1); color: var(--brand-blue); display: flex; align-items: center; justify-content: center; }
    .shadow-blue-light { box-shadow: 0 10px 20px rgba(59, 130, 246, 0.1); }

    @media (max-width: 600px) {
        .form-grid { grid-template-columns: 1fr; }
        .form-actions { flex-direction: column-reverse; }
        .btn-primary, .btn-cancel { justify-content: center; }
        .page-header { flex-direction: column; align-items: flex-start; }
        .btn-secondary { width: 100%; justify-content: center; }
    }
</style>
@endsection
