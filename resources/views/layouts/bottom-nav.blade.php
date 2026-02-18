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

<!-- Mobile Bottom Navigation -->
<div class="mobile-bottom-nav xl:hidden">
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
            
            <!-- Sidebar Toggle / More -->
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
        position: fixed;
        bottom: calc(15px + env(safe-area-inset-bottom));
        left: 20px;
        right: 20px;
        z-index: 50;
        pointer-events: none;
    }

    .bottom-nav-glass {
        background: rgba(255, 255, 255, 0.85);
        backdrop-filter: blur(20px);
        -webkit-backdrop-filter: blur(20px);
        border: 1px solid rgba(255, 255, 255, 0.3);
        border-radius: 25px;
        padding: 10px 15px;
        padding-bottom: calc(10px + env(safe-area-inset-bottom));
        pointer-events: auto;
        max-width: 500px;
        margin: 0 auto;
    }

    .dark .bottom-nav-glass {
        background: rgba(31, 41, 55, 0.85);
        border-color: rgba(255, 255, 255, 0.08);
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
        color: var(--text-dim);
        transition: all 0.3s cubic-bezier(0.175, 0.885, 0.32, 1.275);
        padding: 5px 10px;
        position: relative;
        min-width: 60px;
    }

    .nav-item.active {
        color: var(--brand-blue);
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
        background: var(--brand-blue);
        margin-top: 2px;
    }

    @media (max-width: 380px) {
        .nav-label { display: none; }
        .nav-item { min-width: 40px; }
    }
</style>
