import { isValidPhone, setupRegistration } from './registration';

const storageKey = 'tourismcloud.frontend.v1';
const roleNames = { admin: 'Administrador', operador: 'Operador turístico', proveedor: 'Proveedor', turista: 'Turista' };
const permissions = { admin: ['usuarios', 'destinos', 'organizaciones'], operador: ['destinos'], proveedor: [], turista: [] };
const initialData = {
    usuarios: [
        { id: 1, nombre: 'Andrea Vargas', email: 'andrea@peruexperiences.demo', rol: 'Administrador', organizacion: 'Perú Experiences', estado: 'Activo' },
        { id: 2, nombre: 'Diego Mendoza', email: 'diego@andesexplorer.demo', rol: 'Operador turístico', organizacion: 'Andes Explorer', estado: 'Activo' },
        { id: 3, nombre: 'Valentina Torres', email: 'valentina@peruexperiences.demo', rol: 'Operador turístico', organizacion: 'Perú Experiences', estado: 'Activo' },
        { id: 4, nombre: 'Mateo Salazar', email: 'mateo@casacolonial.demo', rol: 'Proveedor', organizacion: 'Casa Colonial', estado: 'Activo' },
        { id: 5, nombre: 'Camila Flores', email: 'camila@viajeros.demo', rol: 'Turista', organizacion: 'Sin organización', estado: 'Activo' },
        { id: 6, nombre: 'Sebastián Rojas', email: 'sebastian@andesexplorer.demo', rol: 'Operador turístico', organizacion: 'Andes Explorer', estado: 'Inactivo' },
    ],
    destinos: [
        { id: 1, nombre: 'Cusco', region: 'Cusco', pais: 'Perú', descripcion: 'Historia viva entre montañas y caminos ancestrales.', organizacion: 'Perú Experiences', estado: 'Activo', paisaje: 'mountain', coordenada: '13.53° S / 71.97° W' },
        { id: 2, nombre: 'Arequipa', region: 'Arequipa', pais: 'Perú', descripcion: 'Arquitectura de sillar y paisajes que sorprenden.', organizacion: 'Andes Explorer', estado: 'Activo', paisaje: 'desert', coordenada: '16.41° S / 71.54° W' },
        { id: 3, nombre: 'Lima', region: 'Lima', pais: 'Perú', descripcion: 'El Pacífico, la ciudad y una gastronomía extraordinaria.', organizacion: 'Perú Experiences', estado: 'Activo', paisaje: 'coast', coordenada: '12.05° S / 77.04° W' },
        { id: 4, nombre: 'Puno', region: 'Puno', pais: 'Perú', descripcion: 'Tradiciones que navegan sobre el lago Titicaca.', organizacion: 'Andes Explorer', estado: 'Inactivo', paisaje: 'coast', coordenada: '15.84° S / 70.02° W' },
    ],
    organizaciones: [
        { id: 1, nombre: 'Perú Experiences', email: 'contacto@peruexperiences.demo', tipo: 'Operador turístico', ciudad: 'Cusco', estado: 'Activo' },
        { id: 2, nombre: 'Andes Explorer', email: 'contacto@andesexplorer.demo', tipo: 'Operador turístico', ciudad: 'Arequipa', estado: 'Activo' },
        { id: 3, nombre: 'Casa Colonial', email: 'contacto@casacolonial.demo', tipo: 'Proveedor', ciudad: 'Cusco', estado: 'Activo' },
    ],
};
const escapeHtml = (value) => String(value ?? '').replace(/[&<>"']/g, character => ({ '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;' })[character]);
let toastTimeout;
function toast(message) { const element = document.getElementById('toast'); element.textContent = message; element.hidden = false; clearTimeout(toastTimeout); toastTimeout = setTimeout(() => { element.hidden = true; }, 4500); }
function readStorage(key, fallback) { try { return JSON.parse(localStorage.getItem(key)) ?? fallback; } catch { return fallback; } }
function writeStorage(key, value) { try { localStorage.setItem(key, JSON.stringify(value)); return true; } catch { toast('El navegador no permite guardar cambios. Se conservarán mientras esta página siga abierta.'); return false; } }
function enterDemo() { writeStorage(`${storageKey}.session`, { role: 'admin' }); window.location.assign('/destinos'); }

if (document.body.dataset.page === 'login') {
    setupRegistration(enterDemo);
    const email = document.getElementById('email');
    const password = document.getElementById('password');
    const remembered = readStorage(`${storageKey}.email`, '');
    email.value = remembered;
    document.getElementById('remember').checked = Boolean(remembered);
    document.getElementById('toggle-password').addEventListener('click', event => {
        const visible = password.type === 'password'; password.type = visible ? 'text' : 'password';
        event.currentTarget.setAttribute('aria-label', visible ? 'Ocultar contraseña' : 'Mostrar contraseña');
        event.currentTarget.setAttribute('aria-pressed', String(visible));
    });
    document.getElementById('demo-access').addEventListener('click', enterDemo);
    document.getElementById('recovery-demo').addEventListener('click', enterDemo);
    document.getElementById('forgot-password').addEventListener('click', () => document.getElementById('recovery-dialog').showModal());
    document.getElementById('login-form').addEventListener('submit', event => {
        event.preventDefault();
        const contact = email.value.trim();
        const error = document.getElementById('login-error');
        if (!( /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(contact) || isValidPhone(contact))) {
            error.hidden = false;
            error.textContent = 'Escribe un correo válido o un teléfono con + y código de país.';
            email.setAttribute('aria-invalid', 'true'); email.focus(); return;
        }
        email.setAttribute('aria-invalid', 'false');
        writeStorage(`${storageKey}.email`, document.getElementById('remember').checked ? email.value : '');
        password.value = '';
        error.hidden = false;
        error.textContent = 'La autenticación todavía no está conectada. Usa “Explorar demo interactiva” para probar el frontend.';
    });
}

if (document.body.dataset.page === 'workspace') {
    const section = document.body.dataset.section;
    const data = readStorage(`${storageKey}.data`, structuredClone(initialData));
    for (const key of Object.keys(initialData)) { if (!Array.isArray(data[key])) data[key] = structuredClone(initialData[key]); }
    let currentRole = readStorage(`${storageKey}.session`, { role: 'admin' }).role;
    if (!roleNames[currentRole]) currentRole = 'admin';
    let status = 'all';
    let editingId = null;
    const dialog = document.getElementById('record-dialog');
    const search = document.getElementById('record-search');
    const roleFilter = document.getElementById('role-filter');
    const roleSelect = document.getElementById('demo-role');
    roleSelect.value = currentRole;
    const canEdit = () => permissions[currentRole].includes(section);
    const initials = (name) => name.trim().split(/\s+/).slice(0, 2).map(part => part[0]).join('').toUpperCase();
    const badge = (value) => `<span class="status-badge ${value === 'Inactivo' ? 'inactive' : ''}">${escapeHtml(value)}</span>`;
    const person = (record, subtitle) => `<div class="person-cell"><span class="person-avatar">${escapeHtml(initials(record.nombre))}</span><div><strong>${escapeHtml(record.nombre)}</strong><small>${escapeHtml(subtitle)}</small></div></div>`;
    const actions = (record) => canEdit() ? `<button class="record-action" data-edit="${record.id}" aria-label="Editar ${escapeHtml(record.nombre)}">Editar ↗</button><button class="record-action" data-toggle="${record.id}" aria-label="${record.estado === 'Activo' ? 'Desactivar' : 'Activar'} ${escapeHtml(record.nombre)}">${record.estado === 'Activo' ? 'Desactivar' : 'Activar'}</button>` : '<span class="role-badge">Solo lectura</span>';
    function filteredRecords() {
        const query = search.value.toLocaleLowerCase('es').normalize('NFD').replace(/\p{Diacritic}/gu, '');
        return (data[section] ?? []).filter(record => {
            const text = Object.values(record).join(' ').toLocaleLowerCase('es').normalize('NFD').replace(/\p{Diacritic}/gu, '');
            return (status === 'all' || record.estado === status) && text.includes(query) && (section !== 'usuarios' || roleFilter.value === 'all' || record.rol === roleFilter.value);
        });
    }
    function renderStats() {
        const records = data[section] ?? [];
        const values = section === 'permisos' ? [['Perfiles disponibles', 4, 'Un acceso para cada función'], ['Módulos de gestión', 3, 'Usuarios, organizaciones y destinos'], ['Perfil actual', roleNames[currentRole], 'Previsualización de permisos']] : [['Total de ' + section, records.length, 'Tu ecosistema, en un solo lugar'], ['Activos', records.filter(item => item.estado === 'Activo').length, 'Listos para conectar'], ['Inactivos', records.filter(item => item.estado !== 'Activo').length, 'Pendientes de reactivación']];
        document.getElementById('stats-grid').innerHTML = values.map(([label, value, caption], index) => `<div class="stat-card" style="animation-delay:${index * 70}ms"><div class="stat-top"><span>${escapeHtml(label)}</span><span>${['◇', '↗', '◷'][index]}</span></div><div class="stat-number" ${typeof value === 'string' ? 'style="font-size:20px"' : ''}>${escapeHtml(value)}</div><span class="stat-foot">${escapeHtml(caption)}</span></div>`).join('');
    }
    function renderPermissions() {
        const rows = [['Ver catálogos', true, true, true, true], ['Gestionar usuarios', true, false, false, false], ['Gestionar organizaciones', true, false, false, false], ['Gestionar destinos asignados', true, true, false, false], ['Asignar roles', true, false, false, false]];
        document.querySelector('.data-toolbar').hidden = true;
        document.getElementById('records').innerHTML = `<div class="table-scroll"><table class="records-table permission-table"><thead><tr><th>Permiso</th><th>Administrador</th><th>Operador</th><th>Proveedor</th><th>Turista</th></tr></thead><tbody>${rows.map(row => `<tr><td>${row[0]}</td>${row.slice(1).map(value => `<td><span class="${value ? 'permission-yes' : 'permission-no'}" aria-label="${value ? 'Permitido' : 'No permitido'}">${value ? '✓' : '—'}</span></td>`).join('')}</tr>`).join('')}</tbody></table></div><p class="permission-caption">Esta matriz es una propuesta visual. La autenticación y la autorización del servidor se conectarán en la fase de backend.</p>`;
        document.getElementById('results-count').textContent = '4 roles · 5 permisos';
    }
    function render() {
        document.getElementById('current-role').textContent = roleNames[currentRole];
        document.getElementById('create-record').hidden = section === 'permisos' || !canEdit();
        renderStats();
        if (section === 'permisos') { renderPermissions(); return; }
        const records = filteredRecords();
        document.getElementById('total-count').textContent = data[section].length;
        document.getElementById('results-count').textContent = `${records.length} de ${data[section].length} ${section}`;
        const target = document.getElementById('records');
        if (!records.length) { target.innerHTML = '<div class="empty-state"><h3>No encontramos resultados.</h3><p>Prueba otro nombre o cambia los filtros.</p><button class="demo-button" id="reset-filters" style="margin:20px auto 0">Limpiar filtros</button></div>'; return; }
        if (section === 'destinos') {
            target.innerHTML = `<div class="destination-grid">${records.map(record => `<article class="destination-card"><div class="destination-landscape ${escapeHtml(record.paisaje || 'mountain')}"><span class="landscape-sun"></span>${badge(record.estado)}<span class="destination-coordinate">${escapeHtml(record.coordenada || record.pais)}</span></div><div class="destination-info"><small>${escapeHtml(record.region)} · ${escapeHtml(record.pais)}</small><h3>${escapeHtml(record.nombre)}</h3><p>${escapeHtml(record.descripcion)}</p><div class="destination-bottom"><span>${escapeHtml(record.organizacion)}</span>${actions(record)}</div></div></article>`).join('')}</div>`;
        } else {
            const user = section === 'usuarios';
            target.innerHTML = `<div class="table-scroll"><table class="records-table"><thead><tr><th>${user ? 'Usuario' : 'Organización'}</th><th>${user ? 'Rol' : 'Tipo'}</th><th>${user ? 'Organización' : 'Ciudad'}</th><th>Estado</th><th>Acciones</th></tr></thead><tbody>${records.map(record => `<tr><td>${person(record, record.email)}</td><td><span class="role-badge">${escapeHtml(user ? record.rol : record.tipo)}</span></td><td>${escapeHtml(user ? record.organizacion : record.ciudad)}</td><td>${badge(record.estado)}</td><td>${actions(record)}</td></tr>`).join('')}</tbody></table></div>`;
        }
    }
    function field(name, label, value, type = 'text', options = null) {
        const control = options ? `<select id="field-${name}" name="${name}">${options.map(option => `<option ${option === value ? 'selected' : ''}>${escapeHtml(option)}</option>`).join('')}</select>` : type === 'textarea' ? `<textarea id="field-${name}" name="${name}" maxlength="500" required>${escapeHtml(value)}</textarea>` : `<input id="field-${name}" name="${name}" type="${type}" value="${escapeHtml(value)}" maxlength="150" required>`;
        return `<label for="field-${name}">${label}</label>${control}`;
    }
    function openEditor(id = null) {
        if (!canEdit()) { toast('Este perfil tiene acceso de solo lectura.'); return; }
        editingId = id;
        const record = data[section].find(item => item.id === id) ?? {};
        const labels = { usuarios: 'usuario', destinos: 'destino', organizaciones: 'organización' };
        document.getElementById('dialog-title').textContent = `${id ? 'Editar' : 'Nueva conexión:'} ${labels[section]}`;
        let fields = field('nombre', section === 'usuarios' ? 'Nombre completo' : 'Nombre', record.nombre || '');
        const organizationOptions = ['Sin organización', ...data.organizaciones.filter(item => item.estado === 'Activo').map(item => item.nombre)];
        if (record.organizacion && !organizationOptions.includes(record.organizacion)) organizationOptions.push(record.organizacion);
        if (section === 'usuarios') fields += field('email', 'Correo electrónico', record.email || '', 'email') + field('rol', 'Rol de acceso', record.rol || 'Turista', 'text', Object.values(roleNames)) + field('organizacion', 'Organización', record.organizacion || organizationOptions[1] || 'Sin organización', 'text', organizationOptions);
        if (section === 'destinos') fields += field('region', 'Región', record.region || '') + field('pais', 'País', record.pais || 'Perú') + field('descripcion', 'Descripción', record.descripcion || '', 'textarea') + field('organizacion', 'Organización', record.organizacion || organizationOptions[1] || 'Sin organización', 'text', organizationOptions);
        if (section === 'organizaciones') fields += field('email', 'Correo de contacto', record.email || '', 'email') + field('tipo', 'Tipo de organización', record.tipo || 'Operador turístico', 'text', ['Operador turístico', 'Proveedor']) + field('ciudad', 'Ciudad', record.ciudad || '');
        fields += field('estado', 'Estado', record.estado || 'Activo', 'text', ['Activo', 'Inactivo']);
        document.getElementById('record-fields').innerHTML = fields;
        dialog.showModal();
    }
    document.getElementById('create-record').addEventListener('click', () => openEditor());
    document.getElementById('close-editor').addEventListener('click', () => dialog.close());
    document.getElementById('cancel-editor').addEventListener('click', () => dialog.close());
    document.getElementById('record-form').addEventListener('submit', event => {
        event.preventDefault();
        if (!canEdit()) return;
        const values = Object.fromEntries(new FormData(event.currentTarget));
        for (const name of Object.keys(values)) values[name] = values[name].trim();
        if (Object.values(values).some(value => !value)) { toast('Completa todos los campos. No pueden contener solo espacios.'); return; }
        if (values.email && data[section].some(record => record.id !== editingId && record.email.toLowerCase() === values.email.toLowerCase())) { toast('Ese correo ya existe en este módulo.'); return; }
        if (editingId) Object.assign(data[section].find(item => item.id === editingId), values);
        else data[section].push({ id: Math.max(0, ...data[section].map(item => item.id)) + 1, ...values });
        writeStorage(`${storageKey}.data`, data); dialog.close(); render(); toast(editingId ? 'Cambios guardados en la demo.' : 'Nueva conexión creada en la demo.');
    });
    document.getElementById('records').addEventListener('click', event => {
        const edit = event.target.closest('[data-edit]');
        if (edit) openEditor(Number(edit.dataset.edit));
        const toggle = event.target.closest('[data-toggle]');
        if (toggle && canEdit()) { const record = data[section].find(item => item.id === Number(toggle.dataset.toggle)); record.estado = record.estado === 'Activo' ? 'Inactivo' : 'Activo'; writeStorage(`${storageKey}.data`, data); render(); toast(`${record.nombre}: ${record.estado.toLowerCase()}.`); }
        if (event.target.closest('#reset-filters')) { search.value = ''; roleFilter.value = 'all'; status = 'all'; document.querySelectorAll('[data-status]').forEach(button => button.classList.toggle('selected', button.dataset.status === 'all')); render(); }
    });
    document.querySelectorAll('[data-status]').forEach(button => button.addEventListener('click', () => { status = button.dataset.status; document.querySelectorAll('[data-status]').forEach(item => item.classList.toggle('selected', item === button)); render(); }));
    search.addEventListener('input', render); roleFilter.addEventListener('change', render);
    roleSelect.addEventListener('change', () => { currentRole = roleSelect.value; writeStorage(`${storageKey}.session`, { role: currentRole }); render(); toast(`Vista de ${roleNames[currentRole].toLowerCase()} activada.`); });
    document.getElementById('logout').addEventListener('click', () => { try { localStorage.removeItem(`${storageKey}.session`); } catch { /* Demo remains usable without storage. */ } window.location.assign('/login'); });
    document.getElementById('mobile-menu').addEventListener('click', event => { const open = document.getElementById('workspace-sidebar').classList.toggle('is-open'); event.currentTarget.setAttribute('aria-expanded', String(open)); });
    document.addEventListener('keydown', event => { if (event.key === 'Escape') { document.getElementById('workspace-sidebar').classList.remove('is-open'); document.getElementById('mobile-menu').setAttribute('aria-expanded', 'false'); } });
    document.addEventListener('click', event => { if (!event.target.closest('#workspace-sidebar') && !event.target.closest('#mobile-menu')) { document.getElementById('workspace-sidebar').classList.remove('is-open'); document.getElementById('mobile-menu').setAttribute('aria-expanded', 'false'); } });
    render();
}
