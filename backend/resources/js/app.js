import { initCurrencyPreference } from './currency';
import { initInterfaceLanguage } from './interface-language';
import './catalog-discovery';

function initPreferenceFormScroll() {
    const form = document.querySelector('[data-preference-form]');
    const storageKey = 'tourismcloud.preferences-form-scroll';
    try {
        const scrollStorage = window.sessionStorage;
        const savedScroll = scrollStorage.getItem(storageKey);
        if (form?.dataset.validationErrors === 'true' && savedScroll !== null) {
            const scrollY = Number(savedScroll);
            scrollStorage.removeItem(storageKey);
            if (Number.isFinite(scrollY)) {
                window.requestAnimationFrame(() => window.scrollTo(0, scrollY));
            }
        } else {
            scrollStorage.removeItem(storageKey);
        }
        form?.addEventListener('submit', () => {
            scrollStorage.setItem(storageKey, String(window.scrollY));
        });
    } catch (error) {
        console.warn('No se pudo conservar la posición del formulario de preferencias.', error);
    }
}

function initPreferences() {
    initPreferenceFormScroll();
    const helpDialog = document.querySelector('[data-help-dialog]');
    const toast = document.querySelector('[data-preference-toast]');
    let lastDialogTrigger = null;
    let toastTimeout;

    const showToast = (message) => {
        if (!toast) return;
        window.clearTimeout(toastTimeout);
        toast.textContent = message;
        toast.hidden = false;
        window.requestAnimationFrame(() => toast.classList.add('is-visible'));
        toastTimeout = window.setTimeout(() => {
            toast.classList.remove('is-visible');
            window.setTimeout(() => { toast.hidden = true; }, 180);
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
        if (!event.target.closest('[data-dropdown]')) closeAllDropdowns();
    });
    document.addEventListener('keydown', (event) => {
        if (event.key === 'Escape') closeAllDropdowns();
    });

    const openDialog = (dialog, trigger) => {
        if (!dialog) return;
        closeAllDropdowns();
        lastDialogTrigger = trigger;
        if (typeof dialog.showModal === 'function') {
            if (!dialog.open) dialog.showModal();
        } else {
            dialog.setAttribute('open', '');
        }
        const focusTarget = dialog.querySelector('button, a[href]');
        window.requestAnimationFrame(() => focusTarget?.focus({ preventScroll: true }));
    };

    if (helpDialog) {
        helpDialog.addEventListener('click', (event) => {
            if (event.target === helpDialog) helpDialog.close?.();
        });
        helpDialog.addEventListener('close', () => {
            const trigger = lastDialogTrigger;
            lastDialogTrigger = null;
            if (trigger?.isConnected) {
                window.requestAnimationFrame(() => trigger.focus({ preventScroll: true }));
            }
        });
    }
    document.querySelectorAll('[data-help-open]').forEach((button) => {
        button.addEventListener('click', () => openDialog(helpDialog, button));
    });
    document.querySelectorAll('[data-help-close]').forEach((button) => {
        button.addEventListener('click', () => helpDialog?.close?.());
    });

    initInterfaceLanguage();
    initCurrencyPreference({ showToast, closeAllDropdowns });
}

if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initPreferences, { once: true });
} else {
    initPreferences();
}
