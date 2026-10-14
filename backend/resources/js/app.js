const tcPreferences = {
    language: 'tourismcloud.interface-language',
    currency: 'tourismcloud.reference-currency',
};

const tcTranslations = {
    es: {
        preferences_label: 'Preferencias',
        language_label: 'Idioma',
        currency_label: 'Moneda',
        help_label: 'Ayuda',
        display_settings: 'Visualización',
        preferences_title: 'Preferencias de la plataforma',
        interface_language: 'Idioma de la interfaz',
        language_description: 'Cambia los controles y mensajes de TourismCloud AI. El contenido publicado conserva su idioma original.',
        reference_currency: 'Moneda de referencia',
        currency_description: 'Los importes guardados se mantienen en soles; el cambio solo modifica la lectura visual.',
        reference_rate: 'Conversión visual usada:',
        help_title: 'Centro de ayuda',
        help_intro: 'Encuentra experiencias, revisa datos referenciales y organiza tu visita desde un solo lugar.',
        help_explore_title: 'Explora el catálogo',
        help_explore_body: 'Usa los filtros por destino, costo y duración para encontrar opciones disponibles.',
        help_explore_action: 'Ver actividades',
        help_data_title: 'Datos referenciales',
        help_data_body: 'Los costos y horarios ayudan a planificar; confirma cualquier reserva directamente con el proveedor.',
        help_data_action: 'Ver rutas',
        help_account_title: 'Tu cuenta',
        help_account_body: 'Crea una cuenta para guardar preferencias y acceder a las herramientas que corresponden a tu perfil.',
        help_account_action_guest: 'Crear una cuenta',
        help_account_action_user: 'Abrir mi espacio',
    },
    en: {
        preferences_label: 'Preferences',
        language_label: 'Language',
        currency_label: 'Currency',
        help_label: 'Help',
        display_settings: 'Display',
        preferences_title: 'Platform preferences',
        interface_language: 'Interface language',
        language_description: 'Change TourismCloud AI controls and messages. Published content keeps its original language.',
        reference_currency: 'Reference currency',
        currency_description: 'Saved amounts remain in soles; this setting only changes how amounts are displayed.',
        reference_rate: 'Visual conversion used:',
        help_title: 'Help center',
        help_intro: 'Find experiences, review reference details and organize your visit from one place.',
        help_explore_title: 'Explore the catalogue',
        help_explore_body: 'Use destination, cost and duration filters to find available options.',
        help_explore_action: 'View activities',
        help_data_title: 'Reference details',
        help_data_body: 'Costs and schedules help you plan; confirm any booking directly with the provider.',
        help_data_action: 'View routes',
        help_account_title: 'Your account',
        help_account_body: 'Create an account to save preferences and access the tools for your profile.',
        help_account_action_guest: 'Create an account',
        help_account_action_user: 'Open my space',
    },
};

const tcPlainTranslations = {
    Inicio: 'Home',
    Explorar: 'Explore',
    Gestión: 'Manage',
    Atractivos: 'Attractions',
    Establecimientos: 'Places',
    Actividades: 'Activities',
    Rutas: 'Routes',
    Ingresar: 'Sign in',
    'Crear cuenta': 'Create account',
    'Crear una cuenta': 'Create an account',
    'Crear mi cuenta': 'Create my account',
    'Iniciar sesión': 'Sign in',
    'Cerrar sesión': 'Sign out',
    'Mi espacio': 'My space',
    Resumen: 'Overview',
    Preferencias: 'Preferences',
    Itinerarios: 'Itineraries',
    Agenda: 'Schedule',
    Usuarios: 'Users',
    Destinos: 'Destinations',
    'Mis atractivos': 'My attractions',
    'Mis actividades': 'My activities',
    'Mis rutas': 'My routes',
    'Mis itinerarios': 'My itineraries',
    'Mis Preferencias': 'My preferences',
    'Panel Operador': 'Operator area',
    'Mis establecimientos': 'My listings',
    'Preferencias recientes': 'Recent preferences',
    Planifica: 'Plan',
    'Para participar': 'Get involved',
    'Volver arriba': 'Back to top',
    'Proyecto académico': 'Academic project',
    'Destinos disponibles': 'Available destinations',
    'Destinos principales': 'Top destinations',
    'Actividades destacadas': 'Featured activities',
    'Explorar todos': 'Explore all',
    'Explorar rutas': 'Explore routes',
    'Ver actividades': 'View activities',
    'Ver rutas': 'View routes',
    'Ver experiencia': 'View experience',
    'Mapa ↗': 'Map ↗',
    Filtros: 'Filters',
    Destino: 'Destination',
    'Todos los destinos': 'All destinations',
    'Costo máximo (S/)': 'Maximum cost (S/)',
    'Entrada referencial (S/)': 'Reference entry (S/)',
    'Categoría de interés': 'Interest category',
    Desde: 'From',
    Hasta: 'Until',
    'Duración máxima (min)': 'Maximum duration (min)',
    Duración: 'Duration',
    Distancia: 'Distance',
    'Ordenar por': 'Sort by',
    'Más recientes': 'Newest',
    'Menor costo': 'Lowest cost',
    'Mayor costo': 'Highest cost',
    'Más corta': 'Shortest',
    'Más larga': 'Longest',
    Paradas: 'Stops',
    Detalles: 'Details',
    'Datos referenciales': 'Reference details',
    'Aplicar filtros': 'Apply filters',
    'Limpiar filtros': 'Clear filters',
    Buscar: 'Search',
    'Correo electrónico': 'Email address',
    Contraseña: 'Password',
    '¿La olvidaste?': 'Forgot it?',
    'Mantener mi sesión iniciada': 'Keep me signed in',
    '¿Aún no tienes una cuenta?': 'New here?',
    'Resultados de actividades': 'Activity results',
    'Resultados de rutas': 'Route results',
    'Resultados de atractivos': 'Attraction results',
    'Resultados de establecimientos': 'Place results',
    'Costo referencial': 'Reference cost',
    'EXPERIENCIA LOCAL': 'LOCAL EXPERIENCE',
    Idioma: 'Language',
    Moneda: 'Currency',
    Ayuda: 'Help',
};

function tcReadPreference(key, fallback) {
    try {
        return window.localStorage.getItem(key) || fallback;
    } catch (_) {
        return fallback;
    }
}

function tcSavePreference(key, value) {
    try {
        window.localStorage.setItem(key, value);
    } catch (_) {
        // The visual preference remains active for this visit even if storage is unavailable.
    }
}

function tcSafeOriginalText(node, property) {
    if (typeof node[property] === 'undefined') {
        node[property] = node.nodeValue;
    }

    return node[property];
}

function tcUpdateStaticInterface(language) {
    const translation = tcTranslations[language] || tcTranslations.es;

    document.querySelectorAll('[data-tc-i18n]').forEach((element) => {
        const key = element.dataset.tcI18n;
        if (translation[key]) {
            element.textContent = translation[key];
        }
    });

    const textWalker = document.createTreeWalker(document.body, NodeFilter.SHOW_TEXT);
    const textNodes = [];
    let node;

    while ((node = textWalker.nextNode())) {
        const parent = node.parentElement;
        if (!parent || parent.closest('script, style, title, textarea, select, option, [data-tc-i18n], [data-tc-translate-ignore]')) {
            continue;
        }

        const original = tcSafeOriginalText(node, '__tcOriginalInterfaceText');
        const leading = original.match(/^\s*/)?.[0] || '';
        const trailing = original.match(/\s*$/)?.[0] || '';
        const key = original.trim();

        if (key && Object.prototype.hasOwnProperty.call(tcPlainTranslations, key)) {
            textNodes.push({ node, original, leading, trailing, key });
        }
    }

    textNodes.forEach(({ node: textNode, original, leading, trailing, key }) => {
        textNode.nodeValue = language === 'en'
            ? `${leading}${tcPlainTranslations[key]}${trailing}`
            : original;
    });
}

function tcUpdateLanguage(language) {
    const validLanguage = language === 'en' ? 'en' : 'es';
    document.documentElement.lang = validLanguage;
    document.documentElement.dataset.tcLanguage = validLanguage;
    document.querySelectorAll('[data-current-language]').forEach((element) => {
        element.textContent = validLanguage === 'en' ? 'English' : 'Español';
    });
    document.querySelectorAll('[data-language-option]').forEach((button) => {
        const isSelected = button.dataset.languageOption === validLanguage;
        button.setAttribute('aria-pressed', String(isSelected));
        button.classList.toggle('is-selected', isSelected);
    });
    tcUpdateStaticInterface(validLanguage);
}

function tcShouldFormatCurrency(node) {
    const parent = node.parentElement;
    if (!parent || !node.nodeValue?.trim()) {
        return false;
    }

    return !parent.closest('script, style, title, textarea, input, select, option, form, code, pre, [data-tc-currency-ignore]');
}

function tcFormatUsd(value, language) {
    const locale = language === 'en' ? 'en-US' : 'es-PE';
    return new Intl.NumberFormat(locale, {
        style: 'currency',
        currency: 'USD',
        minimumFractionDigits: 2,
        maximumFractionDigits: 2,
    }).format(value);
}

function tcUpdateCurrency(currency, language) {
    const selectedCurrency = currency === 'USD' ? 'USD' : 'PEN';
    const moneyPattern = /S\/\.?\s*([0-9][0-9.,]*)/g;
    const walker = document.createTreeWalker(document.body, NodeFilter.SHOW_TEXT);
    const textNodes = [];
    let node;

    while ((node = walker.nextNode())) {
        if (tcShouldFormatCurrency(node)) {
            const original = tcSafeOriginalText(node, '__tcOriginalCurrencyText');
            if (moneyPattern.test(original)) {
                textNodes.push({ node, original });
            }
            moneyPattern.lastIndex = 0;
        }
    }

    textNodes.forEach(({ node: textNode, original }) => {
        textNode.nodeValue = selectedCurrency === 'USD'
            ? original.replace(moneyPattern, (_, amount) => {
                const soles = Number.parseFloat(amount.replace(/,/g, ''));
                return Number.isFinite(soles) ? tcFormatUsd(soles / 3.8, language) : _;
            })
            : original;
    });

    document.documentElement.dataset.tcCurrency = selectedCurrency;
    document.querySelectorAll('[data-current-currency]').forEach((element) => {
        element.textContent = selectedCurrency === 'USD' ? '$ USD' : 'S/ PEN';
    });
    document.querySelectorAll('[data-currency-option]').forEach((button) => {
        const isSelected = button.dataset.currencyOption === selectedCurrency;
        button.setAttribute('aria-pressed', String(isSelected));
        button.classList.toggle('is-selected', isSelected);
    });
}

function tcInitPreferences() {
    const helpDialog = document.querySelector('[data-help-dialog]');
    const toast = document.querySelector('[data-preference-toast]');
    let lastDialogTrigger = null;
    let toastTimeout;
    let language = tcReadPreference(tcPreferences.language, 'es');
    let currency = tcReadPreference(tcPreferences.currency, 'PEN');

    if (!['es', 'en'].includes(language)) {
        language = 'es';
    }
    if (!['PEN', 'USD'].includes(currency)) {
        currency = 'PEN';
    }

    const showToast = (message) => {
        if (!toast) {
            return;
        }

        window.clearTimeout(toastTimeout);
        toast.textContent = message;
        toast.hidden = false;
        window.requestAnimationFrame(() => toast.classList.add('is-visible'));
        toastTimeout = window.setTimeout(() => {
            toast.classList.remove('is-visible');
            window.setTimeout(() => {
                toast.hidden = true;
            }, 180);
        }, 2500);
    };

    const closeAllDropdowns = () => {
        document.querySelectorAll('[data-dropdown]').forEach((wrap) => {
            const trigger = wrap.querySelector('[data-dropdown-trigger]');
            const menu = wrap.querySelector('.tc-dropdown-menu');
            if (trigger && menu) {
                trigger.setAttribute('aria-expanded', 'false');
                menu.hidden = true;
            }
        });
    };

    // Inicializar menús desplegables para Idioma y Moneda
    document.querySelectorAll('[data-dropdown]').forEach((wrap) => {
        const trigger = wrap.querySelector('[data-dropdown-trigger]');
        const menu = wrap.querySelector('.tc-dropdown-menu');
        if (!trigger || !menu) return;

        trigger.addEventListener('click', (event) => {
            event.stopPropagation();
            const isOpen = trigger.getAttribute('aria-expanded') === 'true';
            closeAllDropdowns();
            if (!isOpen) {
                trigger.setAttribute('aria-expanded', 'true');
                menu.hidden = false;
            }
        });
    });

    document.addEventListener('click', (event) => {
        if (!event.target.closest('[data-dropdown]')) {
            closeAllDropdowns();
        }
    });

    document.addEventListener('keydown', (event) => {
        if (event.key === 'Escape') {
            closeAllDropdowns();
        }
    });

    const openDialog = (dialog, trigger) => {
        if (!dialog) {
            return;
        }

        closeAllDropdowns();
        lastDialogTrigger = trigger;
        if (typeof dialog.showModal === 'function') {
            if (!dialog.open) {
                dialog.showModal();
            }
        } else {
            dialog.setAttribute('open', '');
        }

        const focusTarget = dialog.querySelector('button, a[href]');
        window.requestAnimationFrame(() => focusTarget?.focus({ preventScroll: true }));
    };

    const bindDialog = (dialog) => {
        if (!dialog) {
            return;
        }

        dialog.addEventListener('click', (event) => {
            if (event.target === dialog) {
                dialog.close?.();
            }
        });
        dialog.addEventListener('close', () => {
            const trigger = lastDialogTrigger;
            lastDialogTrigger = null;
            if (trigger?.isConnected) {
                window.requestAnimationFrame(() => trigger.focus({ preventScroll: true }));
            }
        });
    };

    bindDialog(helpDialog);

    document.querySelectorAll('[data-help-open]').forEach((button) => {
        button.addEventListener('click', () => openDialog(helpDialog, button));
    });
    document.querySelectorAll('[data-help-close]').forEach((button) => {
        button.addEventListener('click', () => helpDialog?.close?.());
    });

    document.querySelectorAll('[data-language-option]').forEach((button) => {
        button.addEventListener('click', () => {
            language = button.dataset.languageOption;
            tcSavePreference(tcPreferences.language, language);
            tcUpdateLanguage(language);
            tcUpdateCurrency(currency, language);
            closeAllDropdowns();
            showToast(language === 'en' ? 'Interface language changed to English.' : 'El idioma cambió a español.');
        });
    });

    document.querySelectorAll('[data-currency-option]').forEach((button) => {
        button.addEventListener('click', () => {
            currency = button.dataset.currencyOption;
            tcSavePreference(tcPreferences.currency, currency);
            tcUpdateCurrency(currency, language);
            closeAllDropdowns();
            showToast(currency === 'USD'
                ? (language === 'en' ? 'Amounts are shown in USD.' : 'Los importes ahora se muestran en dólares (USD).')
                : (language === 'en' ? 'Amounts are now shown in soles (PEN).' : 'Los importes ahora se muestran en soles (PEN).'));
        });
    });

    tcUpdateLanguage(language);
    tcUpdateCurrency(currency, language);
}

if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', tcInitPreferences, { once: true });
} else {
    tcInitPreferences();
}
