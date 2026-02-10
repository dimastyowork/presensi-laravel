@extends('layouts.app')

@section('content')
<div class="settings-container" x-data>
    <div class="settings-header">
        <h1 class="page-title">Pengaturan <span class="text-brand">Global</span></h1>
        <p class="page-subtitle">Konfigurasi sistem presensi dan notifikasi</p>
    </div>

    <div class="settings-card glass">
        <form action="{{ route('settings.update') }}" method="POST">
            @csrf
            @method('PUT')

            <div class="settings-grid">
                
                <div class="setting-group">
                    <label class="setting-label">Lokasi Kantor (Lat, Lng)</label>
                    <input type="text" name="office_location" 
                           value="{{ $settings->where('key', 'office_location')->first()->value ?? '' }}" 
                           class="setting-input" placeholder="-7.xxx, 108.xxx">
                    <p class="setting-desc">Koordinat pusat untuk validasi lokasi presensi.</p>
                </div>

                <div class="setting-group">
                    <label class="setting-label">Radius (Meter)</label>
                    <input type="number" name="office_radius" 
                           value="{{ $settings->where('key', 'office_radius')->first()->value ?? '500' }}" 
                           class="setting-input">
                    <p class="setting-desc">Jarak maksimal dari titik pusat yang diperbolehkan.</p>
                </div>

                <div class="setting-group full-width">
                    <label class="setting-label">Pesan Presensi</label>
                    <textarea name="attendance_message" rows="3" class="setting-input">{{ $settings->where('key', 'attendance_message')->first()->value ?? '' }}</textarea>
                    <p class="setting-desc">Pesan ini akan muncul di halaman presensi karyawan.</p>
                </div>

                <div class="full-width mt-4" style="border-top: 1px dashed var(--card-border); padding-top: 20px;">
                    <h3 class="setting-label mb-4" style="font-size: 1.1rem; color: var(--brand-blue);">Tombol Link Eksternal / Darurat</h3>
                    <p class="setting-desc mb-4">Tambahkan tombol khusus untuk link eksternal (misal: Form Cuti, Trello, Presensi Darurat, dll).</p>
                </div>

                <div class="full-width" x-data="{
                    links: {{ $settings->where('key', 'external_links')->first()->value ?? '[]' }},
                    addLink() {
                        this.links.push({ label: '', url: '' });
                    },
                    removeLink(index) {
                        this.links.splice(index, 1);
                    }
                }">
                    <!-- Hidden Input for Form Submission -->
                    <input type="hidden" name="external_links" :value="JSON.stringify(links)">

                    <template x-for="(link, index) in links" :key="index">
                        <div class="glass p-4 rounded-xl mb-4 border border-gray-200 dark:border-gray-700 relative transition-all hover:border-blue-400 dark:hover:border-blue-500">
                            <button type="button" @click="removeLink(index)" class="btn-delete-repeater" title="Hapus Link">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                            </button>
                            
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div class="flex flex-col gap-2">
                                    <label class="setting-label-small">Label Tombol</label>
                                    <input type="text" x-model="link.label" class="setting-input w-full" placeholder="Contoh: Form Cuti">
                                </div>
                                <div class="flex flex-col gap-2">
                                    <label class="setting-label-small">URL Link</label>
                                    <input type="text" x-model="link.url" class="setting-input w-full" placeholder="https://...">
                                </div>
                            </div>
                        </div>
                    </template>

                    <button type="button" @click="addLink()" class="btn-add-repeater w-full py-3 border-dashed border-2 flex justify-center items-center gap-2 transition-all rounded-xl">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                        <span>Tambah Tombol Link</span>
                    </button>
                </div>

            </div>

            <div class="form-actions">
                <button type="submit" class="btn-primary">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                    </svg>
                    Simpan Perubahan
                </button>
            </div>
        </form>
    </div>
</div>

<style>
    :root {
        --brand-blue: #3b82f6;
        --text-main: #1e293b;
        --text-secondary: #64748b;
        --text-dim: #94a3b8;
        --bg-primary: #ffffff;
        --card-border: rgba(0, 0, 0, 0.08);
        --glass-bg: rgba(255, 255, 255, 0.8);
    }

    .dark {
        --text-main: #f8fafc;
        --text-secondary: #cbd5e1;
        --text-dim: #9ca3af;
        --bg-primary: #0f172a;
        --card-border: rgba(255, 255, 255, 0.08);
        --glass-bg: rgba(31, 41, 55, 0.5);
    }

    .page-title {
        color: var(--text-main);
        font-size: 2rem;
        font-weight: 800;
        margin-bottom: 0.5rem;
    }
    
    .page-subtitle {
        color: var(--text-secondary);
    }

    .text-brand {
        color: var(--brand-blue);
    }

    .settings-container {
        max-width: 800px;
        margin: 0 auto;
        padding: 40px 20px;
        font-family: 'Outfit', sans-serif;
    }

    .settings-header {
        margin-bottom: 30px;
        text-align: center;
    }

    .settings-card {
        padding: 40px;
        border-radius: 24px;
        border: 1px solid var(--card-border);
        background: var(--glass-bg);
        backdrop-filter: blur(20px);
    }
    
    .dark .settings-card {
        background: rgba(31, 41, 55, 0.6);
        border-color: rgba(255, 255, 255, 0.08);
    }

    .settings-grid {
        display: grid;
        grid-template-columns: 1fr;
        gap: 30px;
        margin-bottom: 30px;
    }
    
    @media (min-width: 768px) {
        .settings-grid {
            grid-template-columns: 1fr 1fr;
        }
    }

    .full-width {
        grid-column: span 1;
    }
    
    @media (min-width: 768px) {
        .full-width {
            grid-column: span 2;
        }
    }

    .setting-group {
        display: flex;
        flex-direction: column;
        gap: 10px;
    }

    .setting-label {
        font-weight: 700;
        color: var(--text-secondary);
        font-size: 0.9rem;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }
    
    .dark .setting-label {
        color: #e2e8f0 !important;
    }

    .setting-input {
        padding: 12px 16px;
        border-radius: 12px;
        border: 1px solid var(--card-border);
        background: var(--bg-primary);
        color: var(--text-main);
        font-size: 1rem;
        transition: all 0.3s;
    }
    
    .dark .setting-input {
        background-color: #1f2937 !important;
        border-color: #374151 !important;
        color: #f3f4f6 !important;
    }

    .setting-input:focus {
        outline: none;
        border-color: var(--brand-blue) !important;
        box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
    }
    
    .dark .setting-input:focus {
        box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.2);
    }

    .setting-desc {
        font-size: 0.85rem;
        color: var(--text-dim);
        margin-top: 4px;
    }
    
    .dark .setting-desc {
        color: #9ca3af;
    }

    .dark input:-webkit-autofill,
    .dark input:-webkit-autofill:hover, 
    .dark input:-webkit-autofill:focus, 
    .dark input:-webkit-autofill:active{
        -webkit-box-shadow: 0 0 0 30px #1f2937 inset !important;
        -webkit-text-fill-color: white !important;
    }

    /* Toggle Switch */
    .toggle-wrapper {
        display: flex;
        align-items: center;
        gap: 16px;
        padding: 10px 0;
    }

    .toggle-switch {
        position: relative;
        display: inline-block;
        width: 60px;
        height: 34px;
    }

    .toggle-switch input {
        opacity: 0;
        width: 0;
        height: 0;
    }

    .slider {
        position: absolute;
        cursor: pointer;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background-color: var(--card-border);
        transition: .4s;
        border-radius: 34px;
    }
    
    .dark .slider {
        background-color: #374151; /* Dark gray for disabled slider */
    }

    .slider:before {
        position: absolute;
        content: "";
        height: 26px;
        width: 26px;
        left: 4px;
        bottom: 4px;
        background-color: white;
        transition: .4s;
        border-radius: 50%;
        box-shadow: 0 2px 4px rgba(0,0,0,0.2);
    }

    input:checked + .slider {
        background-color: var(--brand-blue);
    }

    input:checked + .slider:before {
        transform: translateX(26px);
    }
    
    .toggle-label {
        font-weight: 600;
        color: var(--text-main);
    }
    
    .dark .toggle-label {
        color: #e5e7eb;
    }

    .form-actions {
        display: flex;
        justify-content: flex-end;
        padding-top: 20px;
        border-top: 1px solid var(--card-border);
    }
    
    .dark .form-actions {
        border-color: rgba(255, 255, 255, 0.08); 
    }

    /* Repeater Styles */
    .setting-label-small {
        font-size: 0.75rem;
        font-weight: 700;
        text-transform: uppercase;
        color: #64748b; /* text-secondary equivalent */
    }

    .dark .setting-label-small {
        color: #94a3b8; /* text-gray-400 */
    }

    .btn-add-repeater {
        color: #64748b;
        border-color: #e2e8f0;
    }
    
    .btn-add-repeater:hover {
        background-color: #f8fafc;
        color: var(--brand-blue);
        border-color: var(--brand-blue);
    }

    .dark .btn-add-repeater {
        color: #94a3b8;
        border-color: #374151;
    }

    .dark .btn-add-repeater:hover {
        background-color: rgba(31, 41, 55, 0.5);
        color: #60a5fa; /* blue-400 */
        border-color: #60a5fa;
    }

    /* Primary Button */
    .btn-primary {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 12px 24px;
        background-color: var(--brand-blue);
        color: white;
        border-radius: 12px;
        font-weight: 600;
        transition: all 0.3s;
        border: none;
        cursor: pointer;
    }

    .btn-primary:hover {
        background-color: #2563eb; /* blue-600 */
        box-shadow: 0 4px 12px rgba(37, 99, 235, 0.3);
    }

    .dark .btn-primary {
        background-color: #3b82f6; /* Ensure blue in dark mode */
        color: white;
    }

    .dark .btn-primary:hover {
        background-color: #60a5fa; /* Lighter blue on hover in dark mode */
        box-shadow: 0 4px 12px rgba(59, 130, 246, 0.4);
    }

    /* Delete Button inside Repeater */
    .btn-delete-repeater {
        position: absolute;
        top: 8px;
        right: 8px;
        padding: 6px;
        border-radius: 8px;
        color: #ef4444; /* red-500 */
        transition: all 0.2s;
        background: transparent;
        border: none;
        cursor: pointer;
    }

    .btn-delete-repeater:hover {
        background-color: #fee2e2; /* red-100 */
        color: #dc2626; /* red-600 */
    }

    .dark .btn-delete-repeater:hover {
        background-color: rgba(239, 68, 68, 0.2);
        color: #fca5a5; /* red-300 */
    }
</style>
@endsection
