@extends('layouts.app')
@section('title', 'Edit Shift')

@section('content')
<div class="shift-form-container">
    
    <!-- Header -->
    <div class="page-header">
        <div>
            <h1 class="page-title">Edit <span class="text-brand">Shift</span></h1>
            <p class="page-subtitle">Perbarui informasi shift kerja</p>
        </div>
        <a href="{{ route('shifts.index') }}" class="btn-secondary">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
            </svg>
            Kembali
        </a>
    </div>

    <!-- Form -->
    <div class="form-card glass">
        <form action="{{ route('shifts.update', $shift->id) }}" method="POST">
            @csrf
            @method('PUT')
            
            <div class="form-grid">
                <!-- Shift Name -->
                <div class="form-group full-width">
                    <label class="form-label">Nama Shift <span class="required">*</span></label>
                    <input type="text" name="name" value="{{ old('name', $shift->name) }}" class="form-input @error('name') error @enderror" placeholder="Contoh: Pagi, Siang, Malam, Full Day" required>
                    @error('name')
                        <span class="error-message">{{ $message }}</span>
                    @enderror
                </div>

                <!-- Start Time -->
                <div class="form-group">
                    <label class="form-label">Jam Masuk <span class="required">*</span></label>
                    <input type="time" name="start_time" value="{{ old('start_time', \Carbon\Carbon::parse($shift->start_time)->format('H:i')) }}" class="form-input @error('start_time') error @enderror" required>
                    @error('start_time')
                        <span class="error-message">{{ $message }}</span>
                    @enderror
                </div>

                <!-- End Time -->
                <div class="form-group">
                    <label class="form-label">Jam Pulang <span class="required">*</span></label>
                    <input type="time" name="end_time" value="{{ old('end_time', \Carbon\Carbon::parse($shift->end_time)->format('H:i')) }}" class="form-input @error('end_time') error @enderror" required>
                    @error('end_time')
                        <span class="error-message">{{ $message }}</span>
                    @enderror
                </div>
            </div>

            <div class="form-actions">
                <button type="submit" class="btn-primary">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                    </svg>
                    Simpan Perubahan
                </button>
                <a href="{{ route('shifts.index') }}" class="btn-cancel">Batal</a>
            </div>
        </form>
    </div>
</div>

@push('styles')
<style>
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
