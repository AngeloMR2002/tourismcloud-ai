const preferenceKey = 'tourismcloud.reference-currency';
const originalText = new WeakMap();
const moneyInputs = new WeakMap();
const moneyPattern = /S\/\.?\s*([0-9][0-9.,]*[0-9]|[0-9])/g;

let activeCurrency = 'PEN';
let currentQuote = null;
let quoteRequest = null;
let selectionVersion = 0;

function language() {
    return document.documentElement.lang === 'en' ? 'en' : 'es';
}

function readPreference() {
    try {
        return window.localStorage.getItem(preferenceKey) === 'USD' ? 'USD' : 'PEN';
    } catch (_) {
        return 'PEN';
    }
}

function savePreference(currency) {
    try {
        window.localStorage.setItem(preferenceKey, currency);
    } catch (_) {
        // The current page can still use the selected currency.
    }
}

function loadQuote() {
    if (!quoteRequest) {
        quoteRequest = fetch('/api/exchange-rate/pen-usd', {
            headers: { Accept: 'application/json' },
            credentials: 'same-origin',
        }).then(async (response) => {
            if (!response.ok) {
                throw new Error('Exchange rate unavailable');
            }

            const data = await response.json();
            const rate = Number(data.rate);
            if (data.base !== 'USD' || data.quote !== 'PEN' || !Number.isFinite(rate) || rate <= 0 || !/^\d{4}-\d{2}-\d{2}$/.test(data.as_of)) {
                throw new Error('Invalid exchange rate');
            }

            return { rate, asOf: data.as_of, stale: Boolean(data.stale) };
        }).catch((error) => {
            quoteRequest = null;
            throw error;
        });
    }

    return quoteRequest;
}

function usdFormat(soles) {
    return new Intl.NumberFormat(language() === 'en' ? 'en-US' : 'es-PE', {
        style: 'currency',
        currency: 'USD',
        minimumFractionDigits: 2,
        maximumFractionDigits: 2,
    }).format(soles / currentQuote.rate);
}

function decimal(value) {
    return Math.round((value + Number.EPSILON) * 100) / 100;
}

function formatInput(value) {
    return String(decimal(value).toFixed(2));
}

function inputState(input) {
    if (!moneyInputs.has(input)) {
        moneyInputs.set(input, {
            currency: 'PEN',
            penValue: input.value,
            dirty: false,
            min: input.getAttribute('min'),
            max: input.getAttribute('max'),
        });
        input.addEventListener('input', () => {
            moneyInputs.get(input).dirty = true;
        });
        input.form?.addEventListener('formdata', (event) => {
            const state = moneyInputs.get(input);
            if (state.currency !== 'USD' || !currentQuote || !input.name || input.value === ''
                || input.matches(':disabled') || !event.formData.has(input.name)) {
                return;
            }

            const penValue = state.dirty
                ? formatInput(Number(input.value) * currentQuote.rate)
                : state.penValue;
            event.formData.set(input.name, penValue);
        });
    }

    return moneyInputs.get(input);
}

function renderInputs(currency) {
    document.querySelectorAll('[data-tc-money-input]').forEach((input) => {
        const state = inputState(input);
        if (state.currency === currency) {
            return;
        }

        if (state.currency === 'USD') {
            state.penValue = state.dirty && input.value !== ''
                ? formatInput(Number(input.value) * currentQuote.rate)
                : state.dirty ? '' : state.penValue;
            input.value = state.penValue;
        } else {
            state.penValue = input.value;
            input.value = input.value === '' ? '' : formatInput(Number(input.value) / currentQuote.rate);
        }

        state.currency = currency;
        state.dirty = false;
        for (const attribute of ['min', 'max']) {
            if (state[attribute] !== null && state[attribute] !== '') {
                const value = Number(state[attribute]);
                // Round inward so a valid displayed amount also satisfies the PEN limit.
                const converted = attribute === 'max'
                    ? Math.floor(value / currentQuote.rate * 100) / 100
                    : Math.ceil(value / currentQuote.rate * 100) / 100;
                input.setAttribute(attribute, currency === 'USD' ? String(converted) : state[attribute]);
            }
        }
    });

    document.querySelectorAll('[data-tc-money-label]').forEach((label) => {
        label.childNodes.forEach((node) => {
            if (node.nodeType !== Node.TEXT_NODE) {
                return;
            }

            const original = originalText.get(node)?.original ?? node.nodeValue;
            originalText.set(node, { original, lastRendered: node.nodeValue });
            const rendered = currency === 'USD' ? original.replace(/\((?:S\/|PEN)\)/g, '(USD $)') : original;
            if (node.nodeValue !== rendered) {
                node.nodeValue = rendered;
            }
            originalText.get(node).lastRendered = rendered;
        });
    });
    document.querySelectorAll('[data-tc-money-symbol]').forEach((element) => {
        element.textContent = currency === 'USD' ? '$' : 'S/';
    });
}

function shouldConvertText(node) {
    const parent = node.parentElement;
    return parent && node.nodeValue?.trim()
        && !parent.closest('script, style, title, textarea, input, select, option, code, pre, [data-tc-currency-ignore], [data-tc-rate-status]');
}

function renderText(currency) {
    const walker = document.createTreeWalker(document.body, NodeFilter.SHOW_TEXT);
    let node;

    while ((node = walker.nextNode())) {
        if (!shouldConvertText(node)) {
            continue;
        }

        const previous = originalText.get(node);
        const original = previous && node.nodeValue === previous.lastRendered ? previous.original : node.nodeValue;
        moneyPattern.lastIndex = 0;
        if (!moneyPattern.test(original)) {
            continue;
        }

        moneyPattern.lastIndex = 0;
        const rendered = currency === 'USD'
            ? original.replace(moneyPattern, (match, amount) => {
                const soles = Number(amount.replace(/,/g, ''));
                return Number.isFinite(soles) ? usdFormat(soles) : match;
            })
            : original;

        originalText.set(node, { original, lastRendered: rendered });
        if (node.nodeValue !== rendered) {
            node.nodeValue = rendered;
        }
    }
}

function renderQuoteStatus(message) {
    document.querySelectorAll('[data-tc-rate-status]').forEach((element) => {
        element.textContent = message;
        element.hidden = !message;
    });
}

function renderControls(currency) {
    document.documentElement.dataset.tcCurrency = currency;
    document.querySelectorAll('[data-current-currency]').forEach((element) => {
        element.textContent = currency === 'USD' ? '$ USD' : 'S/ PEN';
    });
    document.querySelectorAll('[data-currency-option]').forEach((button) => {
        const selected = button.dataset.currencyOption === currency;
        button.setAttribute('aria-pressed', String(selected));
        button.classList.toggle('is-selected', selected);
    });
}

function renderCurrency(currency) {
    activeCurrency = currency;
    renderText(currency);
    renderInputs(currency);
    renderControls(currency);

    if (currentQuote && currency === 'USD') {
        const date = new Intl.DateTimeFormat(language() === 'en' ? 'en-US' : 'es-PE', { dateStyle: 'medium', timeZone: 'UTC' })
            .format(new Date(`${currentQuote.asOf}T12:00:00Z`));
        const prefix = currentQuote.stale
            ? (language() === 'en' ? 'Last available rate' : 'Última tasa disponible')
            : (language() === 'en' ? 'Reference rate' : 'Tasa referencial');
        renderQuoteStatus(`${prefix}: 1 USD = S/ ${currentQuote.rate.toFixed(3)} · BCRP/SBS · ${date}.`);
    } else {
        renderQuoteStatus('');
    }
}

async function selectCurrency(currency, showToast) {
    const version = ++selectionVersion;
    currency = currency === 'USD' ? 'USD' : 'PEN';
    if (currency === 'USD') {
        renderQuoteStatus(language() === 'en' ? 'Loading BCRP exchange rate…' : 'Consultando tipo de cambio del BCRP…');
        try {
            const quote = await loadQuote();
            if (version !== selectionVersion) {
                return false;
            }
            currentQuote = quote;
        } catch (_) {
            if (version !== selectionVersion) {
                return false;
            }
            savePreference('PEN');
            renderCurrency('PEN');
            renderQuoteStatus(language() === 'en'
                ? 'A recent rate is unavailable; amounts remain in soles.'
                : 'No hay una tasa reciente disponible; los importes siguen en soles.');
            showToast?.(language() === 'en'
                ? 'USD cannot be displayed without a recent exchange rate.'
                : 'No se puede mostrar USD sin un tipo de cambio reciente.');
            return false;
        }
    }

    renderCurrency(currency);
    savePreference(currency);
    return true;
}

export function initCurrencyPreference({ showToast, closeAllDropdowns }) {
    window.addEventListener('storage', (event) => {
        if (event.key === preferenceKey || event.key === null) {
            void selectCurrency(readPreference(), showToast);
        }
    });
    document.querySelectorAll('[data-tc-money-input]').forEach(inputState);
    document.querySelectorAll('[data-currency-option]').forEach((button) => {
        button.addEventListener('click', async () => {
            closeAllDropdowns();
            if (await selectCurrency(button.dataset.currencyOption, showToast)) {
                showToast?.(activeCurrency === 'USD'
                    ? (language() === 'en' ? 'Amounts are shown in USD.' : 'Los importes se muestran en dólares (USD).')
                    : (language() === 'en' ? 'Amounts are shown in soles (PEN).' : 'Los importes se muestran en soles (PEN).'));
            }
        });
    });

    renderCurrency('PEN');
    if (readPreference() === 'USD') {
        void selectCurrency('USD', showToast);
    }

    const observer = new MutationObserver(() => renderText(activeCurrency));
    observer.observe(document.body, { subtree: true, childList: true, characterData: true });
}
