@extends('layouts.app')
@section('title', 'Edit Unit')

@section('content')
<div class="unit-form-container">
    
    <div class="page-header">
        <div>
            <h1 class="page-title">Edit <span class="text-brand">Unit</span></h1>
            <p class="page-subtitle">Perbarui informasi unit</p>
        </div>
        <a href="{{ route('units.index') }}" class="btn-secondary">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
            </svg>
            Kembali
        </a>
    </div>

    <div class="form-card glass">
        <form action="{{ route('units.update', $unit->id) }}" method="POST">
            @csrf
            @method('PUT')
            
            <div class="form-grid">
                <div class="form-group full-width">
                    <label class="form-label">Unit (SSO)</label>
                    <input type="text" value="{{ $unit->name }} (ID: {{ $unit->id }})" class="form-input" readonly>
                </div>

                <div class="form-group full-width">
                    <label class="form-label">Hari Kerja <span class="required">*</span></label>
                    <p class="form-hint">Pilih hari kerja yang berlaku untuk unit ini (Kosongkan jika berlaku setiap hari).</p>
                    <div class="days-checkbox-grid">
                        @php $currentDays = old('working_days', $unit->working_days) ?: []; @endphp
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
            </div>

            <div class="form-actions">
                <button type="submit" class="btn-primary">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                    </svg>
                    Update Unit
                </button>
                <a href="{{ route('units.index') }}" class="btn-cancel">Batal</a>
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
        --brand-green: #10b981;
        
        /* Light mode */
        --text-main: #1e293b;
        --text-secondary: #64748b;
        --text-dim: #94a3b8;
        --card-bg: #ffffff;
        --card-border: rgba(0, 0, 0, 0.08);
        --glass-bg: rgba(255, 255, 255, 0.8);
        --hover-bg: rgba(0, 0, 0, 0.03);
        --input-bg: rgba(0, 0, 0, 0.02);
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
        --input-bg: rgba(255, 255, 255, 0.03);
    }

    .unit-form-container {
        max-width: 900px;
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
        font-size: clamp(2rem, 4vw, 2.5rem);
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

    .btn-secondary {
        display: flex;
        align-items: center;
        gap: 8px;
        padding: 12px 24px;
        background: var(--hover-bg);
        color: var(--text-main);
        border: 1px solid var(--card-border);
        border-radius: 12px;
        font-weight: 700;
        text-decoration: none;
        transition: all 0.3s;
    }

    .btn-secondary:hover {
        background: var(--card-bg);
        border-color: var(--brand-blue);
    }

    .form-card {
        background: var(--glass-bg);
        border: 1px solid var(--card-border);
        border-radius: 24px;
        padding: 40px;
        backdrop-filter: blur(20px);
    }

    .form-grid {
        display: grid;
        gap: 24px;
        margin-bottom: 32px;
    }

    .form-group {
        display: flex;
        flex-direction: column;
        gap: 8px;
    }

    .form-group.full-width {
        grid-column: 1 / -1;
    }

    .form-label {
        font-size: 0.875rem;
        font-weight: 700;
        color: var(--text-secondary);
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .required {
        color: var(--brand-red);
    }

    .form-input {
        padding: 12px 16px;
        background: var(--input-bg);
        border: 1px solid var(--card-border);
        border-radius: 12px;
        color: var(--text-main);
        font-size: 1rem;
        transition: all 0.3s;
    }

    .form-input:focus {
        outline: none;
        border-color: var(--brand-blue);
        background: var(--card-bg);
    }

    .form-input.error {
        border-color: var(--brand-red);
    }

    .form-hint {
        font-size: 0.875rem;
        color: var(--text-dim);
        margin-bottom: 12px;
    }

    .days-checkbox-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(120px, 1fr));
        gap: 12px;
        margin-bottom: 8px;
    }

    .day-checkbox-item {
        display: flex;
        align-items: center;
        gap: 10px;
        padding: 10px 14px;
        background: var(--input-bg);
        border: 1px solid var(--card-border);
        border-radius: 10px;
        cursor: pointer;
        transition: all 0.2s;
    }

    .day-checkbox-item:hover {
        background: var(--hover-bg);
        border-color: var(--brand-blue);
    }

    .day-checkbox-item input[type="checkbox"] {
        width: 18px;
        height: 18px;
        accent-color: var(--brand-blue);
        cursor: pointer;
    }

    .day-name {
        font-size: 0.9375rem;
        font-weight: 600;
        color: var(--text-main);
    }

    .shifts-list {
        display: flex;
        flex-direction: column;
        gap: 16px;
    }

    .shift-item {
        padding: 20px;
        border: 1px solid var(--card-border);
        border-radius: 16px;
        background: var(--input-bg);
    }

    .shift-grid {
        display: grid;
        grid-template-columns: 2fr 1fr 1fr 40px;
        gap: 16px;
        align-items: end;
    }

    .form-label-small {
        font-size: 0.75rem;
        font-weight: 700;
        color: var(--text-secondary);
        text-transform: uppercase;
        margin-bottom: 4px;
    }

    .form-input-small {
        padding: 10px 14px;
        background: var(--card-bg);
        border: 1px solid var(--card-border);
        border-radius: 10px;
        color: var(--text-main);
        font-size: 0.9375rem;
        width: 100%;
        transition: all 0.3s;
    }

    .form-input-small:focus {
        outline: none;
        border-color: var(--brand-blue);
    }

    .btn-icon-delete {
        padding: 8px;
        background: rgba(239, 68, 68, 0.1);
        color: var(--brand-red);
        border: none;
        border-radius: 10px;
        cursor: pointer;
        transition: all 0.3s;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .btn-icon-delete:hover {
        background: var(--brand-red);
        color: white;
    }

    .flex-center {
        display: flex;
        align-items: center;
        justify-content: center;
        height: 100%;
    }

    .btn-add-shift {
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        padding: 12px;
        background: rgba(16, 185, 129, 0.1);
        color: var(--brand-green);
        border: 2px dashed rgba(16, 185, 129, 0.3);
        border-radius: 12px;
        font-weight: 700;
        cursor: pointer;
        transition: all 0.3s;
    }

    .btn-add-shift:hover {
        background: rgba(16, 185, 129, 0.2);
        border-color: var(--brand-green);
    }

    .error-message {
        font-size: 0.875rem;
        color: var(--brand-red);
        font-weight: 600;
    }

    .form-actions {
        display: flex;
        gap: 12px;
        justify-content: flex-end;
    }

    .btn-primary {
        display: flex;
        align-items: center;
        gap: 8px;
        padding: 14px 28px;
        background: var(--brand-blue);
        color: white;
        border-radius: 12px;
        font-weight: 700;
        border: none;
        cursor: pointer;
        transition: all 0.3s;
    }

    .btn-primary:hover {
        background: var(--brand-blue-dark);
        transform: translateY(-2px);
        box-shadow: 0 10px 20px rgba(59, 130, 246, 0.3);
    }

    .btn-cancel {
        display: flex;
        align-items: center;
        padding: 14px 28px;
        background: var(--hover-bg);
        color: var(--text-main);
        border: 1px solid var(--card-border);
        border-radius: 12px;
        font-weight: 700;
        text-decoration: none;
        transition: all 0.3s;
    }

    .btn-cancel:hover {
        background: var(--card-bg);
        border-color: var(--brand-red);
        color: var(--brand-red);
    }

    .glass {
        backdrop-filter: blur(20px);
    }

    @media (max-width: 768px) {
        .unit-form-container {
            padding: 40px 15px 30px;
        }

        .page-header {
            flex-direction: column;
        }

        .btn-secondary {
            width: 100%;
            justify-content: center;
        }

        .form-card {
            padding: 24px;
        }

        .form-actions {
            flex-direction: column-reverse;
        }

        .btn-primary, .btn-cancel {
            width: 100%;
            justify-content: center;
        }
    }
</style>
@endpush
@endsection
