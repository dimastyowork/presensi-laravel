@php
    $user = auth()->user();
    $unit = mb_strtoupper(trim((string) ($user->unit ?? '')));
    $isAdmin = $user && in_array($unit, ['IT', 'HRD', 'SDM & DIKLAT'], true);

    $navItems = [
        [
            'name' => 'Presensi',
            'icon' => 'calendar',
            'path' => '/presensi',
        ],
        [
            'name' => 'Lembur',
            'icon' => 'briefcase',
            'path' => '/overtime',
        ],
        [
            'name' => 'Riwayat',
            'icon' => 'clock',
            'path' => '/presensi/riwayat',
        ],
    ];

    if ($isAdmin) {
        $navItems[] = [
            'name' => 'Admin',
            'icon' => 'chart-bar',
            'path' => '/laporan-hrd',
        ];
    }
@endphp

<div class="mobile-bottom-nav xl:hidden" id="mobileBottomNav">
    <div class="bottom-nav-glass shadow-premium">
        <div class="nav-items-wrapper">
            @foreach($navItems as $item)
                @php
                    $isActive = request()->is(ltrim($item['path'], '/')) || (request()->is('admin/overtime*') && $item['name'] == 'Admin');
                @endphp
                <a href="{{ $item['path'] }}" class="nav-item {{ $isActive ? 'active' : '' }}">
                    <div class="nav-icon-wrapper">
                        {!! \App\Helpers\MenuHelper::getIconSvg($item['icon']) !!}
                    </div>
                    <span class="nav-label">{{ $item['name'] }}</span>
                    @if($isActive)
                        <div class="active-dot"></div>
                    @endif
                </a>
            @endforeach
            
            <button @click="$store.sidebar.toggleMobileOpen()" class="nav-item">
                <div class="nav-icon-wrapper">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M4 6h16M4 12h16m-7 6h7" stroke-width="2.5" stroke-linecap="round"/></svg>
                </div>
                <span class="nav-label">Menu</span>
            </button>
        </div>
    </div>
</div>

<style>
    .mobile-bottom-nav {
        --bottom-nav-inactive: #94a3b8;
        --bottom-nav-active: #3b82f6;
        position: fixed;
        bottom: 0;
        left: 0;
        right: 0;
        z-index: 50;
        pointer-events: none;
        transition: transform 0.28s ease, opacity 0.28s ease;
        will-change: transform, opacity;
    }

    .bottom-nav-glass {
        background: #ffffff;
        border-top: 1px solid #e2e8f0;
        padding: 12px 15px;
        padding-bottom: calc(12px + env(safe-area-inset-bottom));
        pointer-events: auto;
        width: 100%;
        margin: 0;
    }

    .dark .bottom-nav-glass {
        background: #111827;
        border-top-color: #374151;
        box-shadow: 0 -4px 20px rgba(0, 0, 0, 0.4);
    }

    .nav-items-wrapper {
        display: flex;
        justify-content: space-around;
        align-items: flex-end;
    }

    .nav-item {
        display: flex;
        flex-direction: column;
        align-items: center;
        gap: 5px;
        text-decoration: none;
        color: var(--bottom-nav-inactive);
        transition: all 0.3s cubic-bezier(0.175, 0.885, 0.32, 1.275);
        padding: 5px 10px;
        position: relative;
        min-width: 60px;
    }

    .nav-item.active {
        color: var(--bottom-nav-active);
        transform: translateY(-8px);
    }

    .nav-icon-wrapper {
        width: 24px;
        height: 24px;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .nav-item.active .nav-icon-wrapper svg {
        filter: drop-shadow(0 0 8px rgba(59, 130, 246, 0.5));
    }

    .nav-label {
        font-size: 10px;
        font-weight: 800;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .active-dot {
        width: 4px;
        height: 4px;
        border-radius: 50%;
        background: var(--bottom-nav-active);
        margin-top: 2px;
    }

    .dark .mobile-bottom-nav {
        --bottom-nav-inactive: #cbd5e1;
    }

    @media (max-width: 400px) {
        .nav-label { 
            font-size: 9px;
            letter-spacing: 0;
        }
        .nav-item { 
            min-width: 50px; 
            padding: 5px 4px;
        }
    }

    @media (max-width: 350px) {
        .nav-label { 
            font-size: 8px;
        }
        .nav-item { 
            min-width: 45px;
            padding: 5px 2px;
        }
    }
</style>





