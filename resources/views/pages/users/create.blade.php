@extends('layouts.app')

@section('content')
<div class="user-form-container">
    
    <!-- Header -->
    <div class="page-header">
        <div>
            <h1 class="page-title">Tambah <span class="text-brand">User Baru</span></h1>
            <p class="page-subtitle">Lengkapi form di bawah untuk menambahkan user</p>
        </div>
        <a href="{{ route('users.index') }}" class="btn-secondary">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
            </svg>
            Kembali
        </a>
    </div>

    <!-- Form -->
    <div class="form-card glass">
        <form action="{{ route('users.store') }}" method="POST">
            @csrf
            
            <div class="form-grid">
                <!-- NIP -->
                <div class="form-group">
                    <label class="form-label">NIP <span class="required">*</span></label>
                    <input type="text" name="nip" value="{{ old('nip') }}" class="form-input @error('nip') error @enderror" placeholder="Masukkan NIP" required>
                    @error('nip')
                        <span class="error-message">{{ $message }}</span>
                    @enderror
                </div>

                <!-- Nama -->
                <div class="form-group">
                    <label class="form-label">Nama Lengkap <span class="required">*</span></label>
                    <input type="text" name="name" value="{{ old('name') }}" class="form-input @error('name') error @enderror" placeholder="Masukkan nama lengkap" required>
                    @error('name')
                        <span class="error-message">{{ $message }}</span>
                    @enderror
                </div>


                <!-- Unit -->
                <div class="form-group">
                    <label class="form-label">Unit/Departemen</label>
                    <select name="unit" class="form-input @error('unit') error @enderror" id="unit-select">
                        <option value="">Pilih Unit</option>
                        @foreach($units as $unit)
                            <option value="{{ $unit->name }}" {{ old('unit') == $unit->name ? 'selected' : '' }}>
                                {{ $unit->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('unit')
                        <span class="error-message">{{ $message }}</span>
                    @enderror
                </div>

                <!-- Password -->
                <div class="form-group">
                    <label class="form-label">Password <span class="required">*</span></label>
                    <input type="password" name="password" class="form-input @error('password') error @enderror" placeholder="Minimal 6 karakter" required>
                    @error('password')
                        <span class="error-message">{{ $message }}</span>
                    @enderror
                </div>

                <!-- Konfirmasi Password -->
                <div class="form-group">
                    <label class="form-label">Konfirmasi Password <span class="required">*</span></label>
                    <input type="password" name="password_confirmation" class="form-input" placeholder="Ulangi password" required>
                </div>
            </div>

            <div class="form-actions">
                <button type="submit" class="btn-primary">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                    </svg>
                    Simpan User
                </button>
                <a href="{{ route('users.index') }}" class="btn-cancel">Batal</a>
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

    .user-form-container {
        max-width: 1000px;
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
        grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
        gap: 24px;
        margin-bottom: 32px;
    }

    .form-group {
        display: flex;
        flex-direction: column;
        gap: 8px;
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

    .unit-selector {
        position: relative;
    }

    .custom-unit-input {
        display: flex;
        gap: 8px;
        align-items: center;
    }

    .custom-unit-input .form-input {
        flex: 1;
    }

    .btn-cancel-custom {
        display: flex;
        align-items: center;
        gap: 6px;
        padding: 12px 16px;
        background: var(--hover-bg);
        color: var(--text-secondary);
        border: 1px solid var(--card-border);
        border-radius: 12px;
        font-weight: 600;
        font-size: 0.875rem;
        cursor: pointer;
        transition: all 0.3s;
        white-space: nowrap;
    }

    .btn-cancel-custom:hover {
        background: rgba(239, 68, 68, 0.1);
        border-color: var(--brand-red);
        color: var(--brand-red);
    }

    @media (max-width: 768px) {
        .user-form-container {
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

        .form-grid {
            grid-template-columns: 1fr;
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
