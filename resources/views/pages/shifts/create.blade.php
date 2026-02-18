@extends('layouts.app')
@section('title', 'Tambah Shift')

@section('content')
<div class="shift-form-container">
    <div class="page-header">
        <div>
            <h1 class="page-title">Tambah <span class="text-brand">Shift</span></h1>
            <p class="page-subtitle">Buat shift kerja baru</p>
        </div>
        <a href="{{ route('shifts.index') }}" class="btn-secondary">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
            </svg>
            Kembali
        </a>
    </div>

    <div class="form-card glass">
        <form action="{{ route('shifts.store') }}" method="POST">
            @csrf

            <div class="form-grid">
                <div class="form-group full-width">
                    <label class="form-label">Nama Shift <span class="required">*</span></label>
                    <input type="text" name="name" value="{{ old('name') }}" class="form-input @error('name') error @enderror" placeholder="Contoh: Pagi, Siang, Malam, Full Day" required>
                    @error('name')
                        <span class="error-message">{{ $message }}</span>
                    @enderror
                </div>

                <div class="form-group">
                    <label class="form-label">Jam Masuk <span class="required">*</span></label>
                    <input type="time" name="start_time" value="{{ old('start_time') }}" class="form-input @error('start_time') error @enderror" required>
                    @error('start_time')
                        <span class="error-message">{{ $message }}</span>
                    @enderror
                </div>

                <div class="form-group">
                    <label class="form-label">Jam Pulang <span class="required">*</span></label>
                    <input type="time" name="end_time" value="{{ old('end_time') }}" class="form-input @error('end_time') error @enderror" required>
                    @error('end_time')
                        <span class="error-message">{{ $message }}</span>
                    @enderror
                </div>

                <div class="form-group full-width">
                    <label class="form-label">Hari Shift</label>
                    <p class="form-hint">Kosongkan jika shift ini berlaku setiap hari.</p>
                    @php $currentDays = old('working_days', []); @endphp
                    <div class="days-checkbox-grid">
                        @foreach(['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu', 'Minggu'] as $day)
                            <label class="day-checkbox-item">
                                <input type="checkbox" name="working_days[]" value="{{ $day }}"
                                    {{ is_array($currentDays) && in_array($day, $currentDays) ? 'checked' : '' }}>
                                <span class="day-name">{{ $day }}</span>
                            </label>
                        @endforeach
                    </div>
                    @error('working_days')
                        <span class="error-message">{{ $message }}</span>
                    @enderror
                </div>
            </div>

            <div class="form-actions">
                <button type="submit" class="btn-primary">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                    </svg>
                    Simpan Shift
                </button>
                <a href="{{ route('shifts.index') }}" class="btn-cancel">Batal</a>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<style>
    :root {
        --brand-blue: #3b82f6;
        --brand-blue-dark: #2563eb;
        --brand-red: #ef4444;
        --text-main: #1e293b;
        --text-secondary: #64748b;
        --text-dim: #94a3b8;
        --card-bg: #ffffff;
        --card-border: rgba(0, 0, 0, 0.08);
        --hover-bg: rgba(0, 0, 0, 0.03);
        --shadow-sm: 0 8px 24px rgba(15, 23, 42, 0.08);
    }

    .dark {
        --text-main: #f8fafc;
        --text-secondary: #94a3b8;
        --text-dim: #64748b;
        --card-bg: #1f2937;
        --card-border: rgba(255, 255, 255, 0.08);
        --hover-bg: rgba(255, 255, 255, 0.05);
        --shadow-sm: 0 8px 24px rgba(0, 0, 0, 0.35);
    }

    .shift-form-container {
        max-width: 800px;
        margin: 0 auto;
        padding: 85px 30px 40px;
    }

    .page-header {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        margin-bottom: 40px;
    }

    .page-title {
        font-size: 1.875rem;
        font-weight: 800;
        color: var(--text-main);
        letter-spacing: -0.025em;
    }

    .text-brand { color: var(--brand-blue); }

    .page-subtitle {
        color: var(--text-secondary);
        margin-top: 4px;
    }

    .btn-secondary {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 10px 18px;
        background: var(--hover-bg);
        color: var(--text-main);
        border: 1px solid var(--card-border);
        border-radius: 12px;
        font-weight: 600;
        text-decoration: none;
        transition: all 0.3s;
    }

    .btn-secondary:hover {
        background: var(--card-bg);
        border-color: var(--brand-blue);
    }

    .form-card {
        background: var(--card-bg);
        border: 1px solid var(--card-border);
        border-radius: 20px;
        padding: 40px;
        box-shadow: var(--shadow-sm);
    }

    .form-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 24px;
        margin-bottom: 32px;
    }

    .form-group.full-width {
        grid-column: span 2;
    }

    .form-label {
        font-size: 0.875rem;
        font-weight: 700;
        color: var(--text-secondary);
        text-transform: uppercase;
        margin-bottom: 8px;
        display: block;
    }

    .form-hint {
        font-size: 0.875rem;
        color: var(--text-dim);
        margin-top: -2px;
        margin-bottom: 10px;
    }

    .required { color: var(--brand-red); }

    .form-input {
        width: 100%;
        padding: 12px 16px;
        background: var(--hover-bg);
        border: 1px solid var(--card-border);
        border-radius: 12px;
        color: var(--text-main);
        transition: all 0.3s;
    }

    .form-input:focus {
        border-color: var(--brand-blue);
        box-shadow: 0 0 0 4px rgba(59, 130, 246, 0.1);
        outline: none;
    }

    .error-message {
        color: var(--brand-red);
        font-size: 0.8125rem;
        margin-top: 4px;
    }

    .days-checkbox-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(120px, 1fr));
        gap: 12px;
        margin-bottom: 4px;
    }

    .day-checkbox-item {
        display: flex;
        align-items: center;
        gap: 10px;
        padding: 10px 14px;
        background: var(--hover-bg);
        border: 1px solid var(--card-border);
        border-radius: 10px;
        cursor: pointer;
        transition: all 0.2s;
    }

    .day-checkbox-item:hover {
        border-color: var(--brand-blue);
    }

    .day-checkbox-item input[type="checkbox"] {
        width: 16px;
        height: 16px;
        accent-color: var(--brand-blue);
    }

    .day-name {
        font-size: 0.9375rem;
        font-weight: 600;
        color: var(--text-main);
    }

    .form-actions {
        display: flex;
        justify-content: flex-end;
        gap: 12px;
        padding-top: 24px;
        border-top: 1px solid var(--card-border);
    }

    .btn-primary {
        background: var(--brand-blue);
        color: white;
        padding: 12px 24px;
        border-radius: 12px;
        font-weight: 700;
        border: none;
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        transition: all 0.3s;
    }

    .btn-primary:hover {
        background: var(--brand-blue-dark);
        transform: translateY(-2px);
    }

    .btn-cancel {
        padding: 12px 24px;
        color: var(--text-secondary);
        font-weight: 600;
        text-decoration: none;
        border-radius: 12px;
        transition: all 0.3s;
    }

    .btn-cancel:hover {
        background: var(--hover-bg);
        color: var(--brand-red);
    }
</style>
@endpush
@endsection
