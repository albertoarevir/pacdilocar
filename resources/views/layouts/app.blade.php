<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Control de Flota') — Depto. L.3.</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <style>
        body { background-color: #f0f2f5; font-family: 'Segoe UI', sans-serif; }
        .sidebar {
            width: 240px; min-height: 100vh; background: #1a2744;
            position: fixed; top: 0; left: 0; z-index: 100;
        }
        .sidebar .brand {
            padding: 1.25rem 1rem;
            background: #111d3a;
            color: #fff;
            font-weight: 700;
            font-size: .9rem;
            border-bottom: 1px solid #2d3f6b;
        }
        .sidebar .nav-link {
            color: #a8b8d8; padding: .6rem 1.2rem;
            font-size: .875rem; border-radius: 0;
            display: flex; align-items: center; gap: .6rem;
        }
        .sidebar .nav-link:hover, .sidebar .nav-link.active {
            background: #2d3f6b; color: #fff;
        }
        .sidebar .nav-section {
            font-size: .7rem; color: #5c7aad; text-transform: uppercase;
            padding: 1rem 1.2rem .3rem; letter-spacing: .08em;
        }
        .main-content { margin-left: 240px; padding: 1.5rem; }
        .topbar {
            background: #fff; padding: .75rem 1.5rem;
            box-shadow: 0 1px 4px rgba(0,0,0,.08);
            margin-bottom: 1.5rem;
            display: flex; align-items: center; justify-content: space-between;
        }
        .stat-card {
            border-radius: 12px; border: none;
            box-shadow: 0 2px 8px rgba(0,0,0,.06);
            transition: transform .15s;
        }
        .stat-card:hover { transform: translateY(-2px); }
        .stat-card .icon { font-size: 2rem; opacity: .85; }
        .badge-OPERATIVO    { background:#198754; }
        .badge-PANNE        { background:#dc3545; }
        .badge-MANTENIMIENTO{ background:#fd7e14; }
        .badge-BAJA         { background:#6c757d; }
        .badge-FUERA_DE_SERVICIO { background:#6f42c1; }
        .badge-ENAJENADO    { background:#0d6efd; }
        .table th { font-size:.8rem; text-transform:uppercase; letter-spacing:.05em; color:#6c757d; }
        .table td { font-size:.875rem; vertical-align:middle; }
        .sidebar .nav-sub { padding-left:2.2rem; }
        .sidebar .nav-sub .nav-link { font-size:.82rem; padding:.4rem 1rem; color:#8da4c8; }
        .sidebar .nav-sub .nav-link:hover, .sidebar .nav-sub .nav-link.active { background:#253660; color:#fff; }
        .sidebar .nav-toggle { cursor:pointer; user-select:none; }
        .sidebar .nav-toggle .toggle-arrow { transition:transform .2s; display:inline-block; margin-left:auto; }
        .sidebar .nav-toggle.open .toggle-arrow { transform:rotate(90deg); }
    </style>
</head>
<body>

<!-- Sidebar -->
<div class="sidebar">
    <div class="brand">
        <i class="bi bi-shield-fill me-2"></i>CONTROL DE FLOTA<br>
        <small class="fw-normal text-secondary">Depto. L.3.</small>
    </div>
    <nav class="mt-2">
        <div class="nav-section">Principal</div>
        <a href="{{ route('dashboard') }}" class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">
            <i class="bi bi-speedometer2"></i> Dashboard
        </a>
        <div class="nav-section">Flota</div>
        <a href="{{ route('vehicles.index') }}" class="nav-link {{ request()->routeIs('vehicles.*') ? 'active' : '' }}">
            <i class="bi bi-truck"></i> Listado Vehículos
        </a>
        <a href="{{ route('maintenance.index') }}" class="nav-link {{ request()->routeIs('maintenance.*') ? 'active' : '' }}">
            <i class="bi bi-tools"></i> Control Taller
        </a>
        {{-- ── Configuración (menú desplegable) ──────────────────────── --}}
        @php
            $configSlugs = [
                'colores'        => ['icon' => 'bi-palette-fill',        'label' => 'Colores'],
                'tipos-vehiculo' => ['icon' => 'bi-truck-front-fill',     'label' => 'Tipos de Vehículo'],
                'marcas'         => ['icon' => 'bi-bookmark-star-fill',   'label' => 'Marcas'],
                'modelos'        => ['icon' => 'bi-card-list',            'label' => 'Modelos'],
                'combustibles'   => ['icon' => 'bi-fuel-pump-fill',       'label' => 'Combustible'],
                'estados'        => ['icon' => 'bi-toggles',              'label' => 'Estados'],
                'funciones'      => ['icon' => 'bi-diagram-3-fill',       'label' => 'Funciones'],
                'financiamiento' => ['icon' => 'bi-cash-coin',            'label' => 'Financiamiento'],
            ];
            $isConfigActive = request()->routeIs('config.*');
            $currentCatalog = request()->route('catalog');
        @endphp
        <div class="nav-section">Configuración</div>
        <div class="nav-link nav-toggle {{ $isConfigActive ? 'open' : '' }}"
             id="configToggle"
             style="cursor:pointer"
             onclick="toggleConfig()">
            <i class="bi bi-gear-fill"></i>
            <span>Tablas de Catálogos</span>
            <i class="bi bi-chevron-right toggle-arrow ms-auto"></i>
        </div>
        <div class="nav-sub" id="configSubmenu" style="{{ $isConfigActive ? '' : 'display:none' }}">
            @foreach($configSlugs as $slug => $info)
                <a href="{{ route('config.index', $slug) }}"
                   class="nav-link {{ $currentCatalog === $slug ? 'active' : '' }}">
                    <i class="bi {{ $info['icon'] }}"></i> {{ $info['label'] }}
                </a>
            @endforeach
        </div>

        <div class="nav-section">API</div>
        <a href="/api/v1/dashboard/summary" target="_blank" class="nav-link">
            <i class="bi bi-braces"></i> API Dashboard
        </a>
        <a href="/api/v1/vehicles" target="_blank" class="nav-link">
            <i class="bi bi-braces"></i> API Vehículos
        </a>
    </nav>
</div>

<!-- Contenido principal -->
<div class="main-content">
    <div class="topbar">
        <h6 class="mb-0 fw-semibold text-dark">@yield('page-title', 'Dashboard')</h6>
        <small class="text-muted">{{ now()->format('d/m/Y H:i') }}</small>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @yield('content')
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.2/dist/chart.umd.min.js"></script>
<script>
function toggleConfig() {
    const sub  = document.getElementById('configSubmenu');
    const btn  = document.getElementById('configToggle');
    const open = sub.style.display !== 'none';
    sub.style.display = open ? 'none' : '';
    btn.classList.toggle('open', !open);
}
</script>
@stack('scripts')
</body>
</html>
