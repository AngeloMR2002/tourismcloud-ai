export function initInterfaceLanguage() {
    const endpoint = document.querySelector('meta[name="tc-locale-endpoint"]')?.content;
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content;
    const options = Array.from(document.querySelectorAll('[data-language-option]'));
    const currentLocale = document.documentElement.lang === 'en' ? 'en' : 'es';

    document.querySelectorAll('[data-current-language]').forEach((label) => {
        label.textContent = currentLocale === 'en' ? 'English' : 'Español';
    });
    options.forEach((option) => {
        const selected = option.dataset.languageOption === currentLocale;
        option.setAttribute('aria-pressed', String(selected));
        option.classList.toggle('is-selected', selected);
    });

    if (!endpoint || !csrfToken) {
        return;
    }

    const errorMessage = document.documentElement.lang === 'en'
        ? 'The language could not be changed. Please try again.'
        : 'No se pudo cambiar el idioma. Inténtalo de nuevo.';

    options.forEach((option) => {
        option.addEventListener('click', async () => {
            const locale = option.dataset.languageOption;
            if (!['es', 'en'].includes(locale) || locale === currentLocale) {
                return;
            }

            options.forEach((button) => { button.disabled = true; });

            try {
                const response = await fetch(endpoint, {
                    method: 'POST',
                    credentials: 'same-origin',
                    headers: {
                        Accept: 'application/json',
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken,
                    },
                    body: JSON.stringify({ locale }),
                });

                if (!response.ok || (await response.json()).locale !== locale) {
                    throw new Error('Language preference was not saved.');
                }

                window.location.reload();
            } catch (_) {
                const toast = document.querySelector('[data-preference-toast]');
                if (toast) {
                    toast.textContent = errorMessage;
                    toast.hidden = false;
                    toast.classList.add('is-visible');
                }
                options.forEach((button) => { button.disabled = false; });
            }
        });
    });
}
