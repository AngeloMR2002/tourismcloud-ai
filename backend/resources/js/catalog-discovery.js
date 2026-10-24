function initCatalogDiscovery() {
    document.querySelectorAll('[data-favorite-form]').forEach((form) => {
        form.addEventListener('submit', async (event) => {
            event.preventDefault();
            const button = form.querySelector('button');
            const message = form.querySelector('[role="status"]');
            button.disabled = true;
            message.hidden = true;
            try {
                const response = await fetch(form.action, {
                    method: 'POST', body: new FormData(form), credentials: 'same-origin',
                    headers: { Accept: 'application/json' },
                });
                if (response.status === 401 || response.status === 419) {
                    window.location.assign('/favoritos/guardar/' + form.dataset.key.replace(':', '/'));
                    return;
                }
                if (!response.ok) throw new Error('Favorite request failed');
                const result = await response.json();
                document.querySelectorAll('[data-favorite-form]').forEach((other) => {
                    if (other.dataset.key !== form.dataset.key) return;
                    const heart = other.querySelector('button');
                    heart.setAttribute('aria-pressed', String(result.saved));
                    heart.setAttribute('aria-label', result.saved ? other.dataset.removeLabel : other.dataset.saveLabel);
                    heart.title = heart.getAttribute('aria-label');
                    other.querySelector('[name="_method"]').value = result.saved ? 'DELETE' : 'POST';
                });
                message.textContent = result.message;
                message.hidden = false;
                if (!result.saved && document.querySelector('[data-favorites-list]')) {
                    window.location.reload();
                } else {
                    window.setTimeout(() => { message.hidden = true; }, 3500);
                }
            } catch (_) {
                message.textContent = form.dataset.error;
                message.hidden = false;
            } finally {
                button.disabled = false;
            }
        });
    });

    const DURATION_STEPS = [
        60, 120, 180, 240, 300, 360, 420, 480, 540, 600,
        660, 720, 780, 840, 900, 960, 1020, 1080, 1140, 1200,
        1260, 1320, 1380, // 1 h. hasta 23 h. (pasos de 1 hora)
        1440, 2880, 4320, 5760, 7200, 8640, 10080, 11520, 12960, 14400, // 1 d. hasta 10 d. (pasos de 1 día)
    ];

    const durationToPosition = (val) => {
        const v = Number(val || 60);
        let closestIndex = 0;
        let minDiff = Infinity;
        for (let i = 0; i < DURATION_STEPS.length; i++) {
            const diff = Math.abs(DURATION_STEPS[i] - v);
            if (diff < minDiff) {
                minDiff = diff;
                closestIndex = i;
            }
        }
        return (closestIndex / (DURATION_STEPS.length - 1)) * 1000;
    };

    const renderers = [];
    document.querySelectorAll('[data-catalog-range]').forEach((range) => {
        const low = range.querySelector('[data-range-value="min"]');
        const high = range.querySelector('[data-range-value="max"]');
        const lowHandle = range.querySelector('[data-range-handle="min"]');
        const highHandle = range.querySelector('[data-range-handle="max"]');
        const ceiling = range.querySelector('[data-range-ceiling]');
        const money = range.dataset.money === 'true';
        const upper = () => Math.max(Number(ceiling.value), Number(low.value), Number(high.value), 1);
        const format = (value) => {
            if (money) return new Intl.NumberFormat(document.documentElement.lang === 'en' ? 'en-US' : 'es-PE', {
                style: 'currency', currency: document.documentElement.dataset.tcCurrency === 'USD' ? 'USD' : 'PEN',
            }).format(value);
            const num = Number(value);
            if (!num) return '0 min.';
            if (num >= 1440) {
                const days = Math.round(num / 1440);
                return `${days} d.`;
            }
            const hours = Math.floor(num / 60);
            const minutes = Math.round(num % 60);
            if (hours > 0 && minutes === 0) {
                return `${hours} h.`;
            }
            if (hours > 0 && minutes > 0) {
                return `${hours} h. ${minutes} min.`;
            }
            return `${minutes} min.`;
        };
        const render = () => {
            const max = upper();
            let lowValue, highValue;
            if (money) {
                lowValue = Number(low.value || 0);
                highValue = high.value === '' ? max : Number(high.value);
                lowHandle.value = String(lowValue / max * 1000);
                highHandle.value = String(highValue / max * 1000);
                lowHandle.setAttribute('aria-valuetext', format(lowValue));
                highHandle.setAttribute('aria-valuetext', high.value === '' ? range.dataset.unlimited : format(highValue));
                range.querySelector('[data-range-summary-min]').textContent = format(lowValue);
                range.querySelector('[data-range-summary-max]').textContent = high.value === '' ? range.dataset.unlimited : format(highValue);
            } else {
                lowValue = low.value === '' ? DURATION_STEPS[0] : Number(low.value);
                highValue = high.value === '' ? DURATION_STEPS[DURATION_STEPS.length - 1] : Number(high.value);
                lowHandle.value = String(durationToPosition(lowValue));
                highHandle.value = String(durationToPosition(highValue));
                lowHandle.setAttribute('aria-valuetext', format(lowValue));
                highHandle.setAttribute('aria-valuetext', format(highValue));
                range.querySelector('[data-range-summary-min]').textContent = format(lowValue);
                range.querySelector('[data-range-summary-max]').textContent = format(highValue);
            }
            range.style.setProperty('--range-start', lowHandle.value / 10 + '%');
            range.style.setProperty('--range-end', highHandle.value / 10 + '%');
            // Keep the lower thumb reachable even when both handles meet at the upper end.
            lowHandle.style.zIndex = Number(lowHandle.value) > 950 ? '3' : '1';
            high.setCustomValidity(high.value !== '' && highValue < lowValue ? range.dataset.error : '');
        };
        for (const [handle, input, isLow] of [[lowHandle, low, true], [highHandle, high, false]]) {
            handle.addEventListener('input', () => {
                const boundary = Number(isLow ? highHandle.value : lowHandle.value);
                const position = isLow ? Math.min(Number(handle.value), boundary) : Math.max(Number(handle.value), boundary);
                if (money) {
                    const amount = upper() * position / 1000;
                    input.value = !isLow && position === 1000 ? '' : String(Math.round(amount * 100) / 100);
                } else {
                    const stepIndex = Math.round((position / 1000) * (DURATION_STEPS.length - 1));
                    const dur = DURATION_STEPS[stepIndex];
                    input.value = isLow && stepIndex === 0 ? '' : (!isLow && stepIndex === DURATION_STEPS.length - 1 ? '' : String(dur));
                }
                input.dispatchEvent(new Event('input', { bubbles: true }));
            });
        }
        low.addEventListener('input', render);
        high.addEventListener('input', render);
        renderers.push(render);
        render();
    });
    new MutationObserver(() => renderers.forEach((render) => render())).observe(document.documentElement, {
        attributes: true, attributeFilter: ['data-tc-currency', 'lang'],
    });
}

if (document.readyState === 'loading') document.addEventListener('DOMContentLoaded', initCatalogDiscovery);
else initCatalogDiscovery();
