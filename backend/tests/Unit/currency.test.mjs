import test from 'node:test';
import assert from 'node:assert/strict';

let fixtureId = 0;

async function setup(fetchQuote) {
    const storage = new Map();
    const formListeners = {};
    const listeners = {};
    const windowListeners = {};
    const attributes = new Map([['min', '0'], ['max', '1000']]);
    const input = {
        name: 'costo', value: '100.00', disabled: false,
        form: { addEventListener: (type, callback) => { formListeners[type] = callback; } },
        addEventListener: (type, callback) => { listeners[type] = callback; },
        getAttribute: (name) => attributes.get(name) ?? null,
        setAttribute: (name, value) => attributes.set(name, value),
        matches: () => input.disabled,
    };
    const buttons = ['PEN', 'USD'].map((currency) => ({
        dataset: { currencyOption: currency },
        addEventListener(type, callback) { this.click = callback; },
        setAttribute() {},
        classList: { toggle() {} },
    }));
    const root = { lang: 'en', dataset: {} };
    const price = { nodeValue: 'From S/ 350.00', parentElement: { closest: () => null } };
    globalThis.window = { addEventListener: (type, callback) => { windowListeners[type] = callback; }, localStorage: {
        getItem: (key) => storage.get(key),
        setItem: (key, value) => storage.set(key, value),
    } };
    globalThis.document = {
        documentElement: root, body: {},
        querySelectorAll: (selector) => selector === '[data-tc-money-input]' ? [input]
            : selector === '[data-currency-option]' ? buttons : [],
        createTreeWalker: () => {
            let visited = false;
            return { nextNode() {
                if (visited) return null;
                visited = true;
                return price;
            } };
        },
    };
    globalThis.NodeFilter = { SHOW_TEXT: 4 };
    globalThis.MutationObserver = class { observe() {} };
    globalThis.fetch = fetchQuote ?? (async () => ({
        ok: true,
        json: async () => ({ base: 'USD', quote: 'PEN', rate: 3.5, as_of: '2026-10-20' }),
    }));
    const { initCurrencyPreference } = await import('../../resources/js/currency.js?test=' + ++fixtureId);
    initCurrencyPreference({ closeAllDropdowns() {} });
    return {
        input, root, attributes, price,
        async otherTabCurrency(currency) {
            storage.set('tourismcloud.reference-currency', currency);
            windowListeners.storage({ key: 'tourismcloud.reference-currency' });
            await new Promise((resolve) => setImmediate(resolve));
        },
        select: (currency) => buttons.find((button) => button.dataset.currencyOption === currency).click(),
        edit(value) { input.value = value; listeners.input(); },
        submit(include = true) {
            const formData = new FormData();
            if (include) formData.set(input.name, input.value);
            formListeners.formdata({ formData });
            return formData;
        },
    };
}

test('currency changes in another tab update amounts and preserve edited money', async () => {
    const page = await setup();
    await page.otherTabCurrency('USD');
    assert.equal(page.root.dataset.tcCurrency, 'USD');
    assert.equal(page.input.value, '28.57');
    page.edit('30.00');
    await page.otherTabCurrency('PEN');
    assert.equal(page.root.dataset.tcCurrency, 'PEN');
    assert.equal(page.input.value, '105.00');
    assert.equal(page.submit().get('costo'), '105.00');
});

test('the latest selection wins when the exchange rate response arrives late', async () => {
    let resolve;
    const page = await setup(() => new Promise((done) => { resolve = done; }));
    const pending = page.select('USD');
    await page.select('PEN');
    resolve({ ok: true, json: async () => ({ base: 'USD', quote: 'PEN', rate: 3.5, as_of: '2026-10-20' }) });
    await pending;
    assert.equal(page.root.dataset.tcCurrency, 'PEN');
    assert.equal(page.input.value, '100.00');
});

test('unchanged dollar input preserves the exact original PEN amount on submission and return', async () => {
    const page = await setup();
    await page.select('USD');
    assert.equal(page.input.value, '28.57');
    assert.equal(page.submit().get('costo'), '100.00');
    assert.equal(page.attributes.get('max'), '285.71');
    await page.select('PEN');
    assert.equal(page.input.value, '100.00');
    assert.equal(page.attributes.get('max'), '1000');
});

test('editing dollars submits soles and preserves the edited value when switching back', async () => {
    const page = await setup();
    await page.select('USD');
    page.edit('30.00');
    assert.equal(page.submit().get('costo'), '105.00');
    await page.select('PEN');
    assert.equal(page.input.value, '105.00');
});

test('disabled and omitted fields are not added to a submitted form', async () => {
    const page = await setup();
    await page.select('USD');
    assert.equal(page.submit(false).has('costo'), false);
    page.input.disabled = true;
    assert.equal(page.submit(false).has('costo'), false);
});

test('a failed quote keeps amounts in PEN', async () => {
    const page = await setup(async () => ({ ok: false }));
    await page.select('USD');
    assert.equal(page.root.dataset.tcCurrency, 'PEN');
    assert.equal(page.input.value, '100.00');
});

test('displayed prices convert to dollars and restore their original soles text', async () => {
    const page = await setup();
    await page.select('USD');
    assert.equal(page.price.nodeValue, 'From $100.00');
    await page.select('PEN');
    assert.equal(page.price.nodeValue, 'From S/ 350.00');
});
