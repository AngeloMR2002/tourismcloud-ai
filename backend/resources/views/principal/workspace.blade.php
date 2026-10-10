<!DOCTYPE html>
<html lang="es"><head><meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1"><title>{{ ucfirst($section) }} · TourismCloud AI</title>@vite(['resources/css/app.css', 'resources/css/principal.css', 'resources/js/principal.js'])</head>
<body class="workspace-page" data-page="workspace" data-section="{{ $section }}">
    <aside class="workspace-sidebar" id="workspace-sidebar">
        <a href="{{ route('principal.destinos') }}" class="brand"><span class="brand-symbol">◎</span>TourismCloud<span class="brand-ai">AI</span></a>
        <div class="organization-chip"><span class="organization-logo">P</span><div>Perú Experiences<small>Espacio de trabajo · Demo</small></div></div>
        <span class="nav-label">ESPACIO DE GESTIÓN</span>
        <nav>
            @foreach(['destinos' => ['◇', 'Destinos'], 'usuarios' => ['♧', 'Usuarios'], 'organizaciones' => ['▦', 'Organizaciones'], 'permisos' => ['⌘', 'Roles y permisos']] as $key => $item)
                <a href="{{ route('principal.'.$key) }}" @if($section === $key) aria-current="page" @endif class="workspace-nav {{ $section === $key ? 'active' : '' }}"><span aria-hidden="true">{{ $item[0] }}</span>{{ $item[1] }}<span class="nav-arrow">↗</span></a>
            @endforeach
        </nav>
        <span class="nav-label">EXPLORAR</span><a class="workspace-nav" href="{{ route('catalogo.atractivos.index') }}"><span>↗</span>Catálogo de atractivos</a><a class="workspace-nav" href="{{ route('catalogo.establecimientos.index') }}"><span>▤</span>Establecimientos</a>
        <div class="sidebar-bottom"><div class="help-card"><span>✧</span><h3>Todo empieza con una conexión.</h3><p>Construye experiencias que merecen ser compartidas.</p></div><button id="logout" class="workspace-nav"><span>↪</span>Salir de la demo</button></div>
    </aside>
    <div class="workspace-main">
        <header class="workspace-topbar"><div><button id="mobile-menu" aria-label="Abrir navegación" aria-expanded="false">☰</button><span>Workspace <span class="breadcrumb-slash">/</span> <strong>{{ $section === 'permisos' ? 'Roles y permisos' : ucfirst($section) }}</strong></span></div><div class="topbar-profile"><span class="preview-badge">DEMO FRONTEND</span><span class="profile-avatar">AV</span><div>Andrea Vargas<small id="current-role">Administradora</small></div></div></header>
        <main class="workspace-content">
            <div class="workspace-heading"><div><span class="form-kicker">CONECTA. ORGANIZA. CRECE.</span><h1>{{ ['usuarios' => 'Personas que hacen posible el viaje.', 'destinos' => 'El próximo destino empieza contigo.', 'organizaciones' => 'Un ecosistema de posibilidades.', 'permisos' => 'Cada persona, el acceso correcto.'][$section] }}</h1><p>{{ ['usuarios' => 'Gestiona tu equipo, sus roles y el acceso a tu organización.', 'destinos' => 'Organiza los lugares que convierten un viaje en una experiencia.', 'organizaciones' => 'Conoce las organizaciones que forman parte de tu red.', 'permisos' => 'Explora cómo se distribuyen los permisos de cada perfil.'][$section] }}</p></div><button id="create-record" class="primary-button" @if($section === 'permisos') hidden @endif>+ {{ ['usuarios' => 'Nuevo usuario', 'destinos' => 'Nuevo destino', 'organizaciones' => 'Nueva organización', 'permisos' => ''][$section] }}</button></div>
            <div class="stats-grid" id="stats-grid"></div>
            <section class="data-section">
                <div class="data-toolbar"><div class="data-tabs"><button class="selected" data-status="all">Todos <span id="total-count"></span></button><button data-status="Activo">Activos</button><button data-status="Inactivo">Inactivos</button></div><div class="search-tools"><label class="workspace-search"><span aria-hidden="true">⌕</span><input id="record-search" type="search" aria-label="Buscar registros" placeholder="Buscar {{ $section }}..."></label><select id="role-filter" aria-label="Filtrar por rol" @if($section !== 'usuarios') hidden @endif><option value="all">Todos los roles</option><option>Administrador</option><option>Operador turístico</option><option>Proveedor</option><option>Turista</option></select></div></div>
                <div id="records"></div><div class="data-footer"><span id="results-count"></span><span>Datos de demostración · Los cambios se guardan en este navegador</span></div>
            </section>
            <section class="permissions-preview"><span class="permission-icon">⌘</span><div><h3>Un espacio para cada rol</h3><p>Cambia de perfil para previsualizar los permisos de la interfaz.</p></div><label><span class="sr-only">Perfil de demostración</span><select id="demo-role"><option value="admin">Administrador</option><option value="operador">Operador turístico</option><option value="proveedor">Proveedor</option><option value="turista">Turista</option></select></label></section>
        </main>
        <footer class="workspace-footer">TourismCloud AI <span>Personas y destinos, conectados.</span></footer>
    </div>
    <dialog id="record-dialog" class="editor-dialog"><form id="record-form"><div class="dialog-heading"><div><span class="form-kicker">TU ESPACIO DE GESTIÓN</span><h2 id="dialog-title">Nuevo registro</h2></div><button type="button" class="dialog-close" id="close-editor" aria-label="Cerrar formulario">×</button></div><div id="record-fields"></div><p class="demo-caption">Demo: estos cambios solo se guardan en este navegador.</p><div class="dialog-actions"><button type="button" class="demo-button" id="cancel-editor">Cancelar</button><button type="submit" class="primary-button">Guardar cambios →</button></div></form></dialog>
    <div id="toast" class="toast" role="status" hidden></div>
</body></html>
