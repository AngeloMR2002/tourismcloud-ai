<dialog id="help-dialog" class="tc-help-dialog" data-help-dialog data-tc-currency-ignore aria-labelledby="help-dialog-title">
    <div class="tc-dialog__header">
        <div>
            <p class="tc-dialog__eyebrow" data-tc-i18n="help_label">Ayuda</p>
            <h2 id="help-dialog-title" data-tc-i18n="help_title">Centro de ayuda</h2>
        </div>
        <button type="button" class="tc-dialog__close" data-help-close aria-label="Cerrar ayuda">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" aria-hidden="true"><path d="m7 7 10 10M17 7 7 17" stroke-width="1.9" stroke-linecap="round"/></svg>
        </button>
    </div>
    <div class="tc-help-dialog__body">
        <p class="tc-help-dialog__lead" data-tc-i18n="help_intro">Encuentra experiencias, revisa datos referenciales y organiza tu visita desde un solo lugar.</p>
        <div class="tc-help-grid">
            <article>
                <span class="tc-help-grid__icon" aria-hidden="true"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor"><path d="M4 6.5A2.5 2.5 0 0 1 6.5 4H10v16H6.5A2.5 2.5 0 0 1 4 17.5v-11ZM20 6.5A2.5 2.5 0 0 0 17.5 4H14v16h3.5a2.5 2.5 0 0 0 2.5-2.5v-11Z" stroke-width="1.7" stroke-linejoin="round"/></svg></span>
                <h3 data-tc-i18n="help_explore_title">Explora el catálogo</h3>
                <p data-tc-i18n="help_explore_body">Usa los filtros por destino, costo y duración para encontrar opciones disponibles.</p>
                <a href="{{ route('catalogo.actividades.index') }}" data-tc-i18n="help_explore_action">Ver actividades</a>
            </article>
            <article>
                <span class="tc-help-grid__icon" aria-hidden="true"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor"><circle cx="12" cy="12" r="8" stroke-width="1.7"/><path d="M12 7v5l3 2" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"/></svg></span>
                <h3 data-tc-i18n="help_data_title">Datos referenciales</h3>
                <p data-tc-i18n="help_data_body">Los costos y horarios ayudan a planificar; confirma cualquier reserva directamente con el proveedor.</p>
                <a href="{{ route('catalogo.rutas.index') }}" data-tc-i18n="help_data_action">Ver rutas</a>
            </article>
            <article>
                <span class="tc-help-grid__icon" aria-hidden="true"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor"><path d="M12 3v18M5 7.5c0-1.4 1.7-2.5 3.8-2.5h6.4c2.1 0 3.8 1.1 3.8 2.5S17.3 10 15.2 10H8.8C6.7 10 5 11.1 5 12.5S6.7 15 8.8 15h6.4c2.1 0 3.8 1.1 3.8 2.5S17.3 20 15.2 20H8.8C6.7 20 5 18.9 5 17.5" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"/></svg></span>
                <h3 data-tc-i18n="help_account_title">Tu cuenta</h3>
                <p data-tc-i18n="help_account_body">Crea una cuenta para guardar preferencias y acceder a las herramientas que corresponden a tu perfil.</p>
                @guest
                    <a href="{{ route('register') }}" data-tc-i18n="help_account_action_guest">Crear una cuenta</a>
                @else
                    <a href="{{ route('dashboard') }}" data-tc-i18n="help_account_action_user">Abrir mi espacio</a>
                @endguest
            </article>
        </div>
    </div>
</dialog>

<div class="tc-preference-toast" data-preference-toast role="status" aria-live="polite" aria-atomic="true" hidden></div>
