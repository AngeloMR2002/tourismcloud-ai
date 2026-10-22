import test from 'node:test';
import assert from 'node:assert/strict';

let fixture = 0;
async function setup({ money = true, min = '', max = '', ceiling = '2000' } = {}) {
    const node = (value = '') => ({
        value, listeners: {}, style: {}, textContent: '', attributes: {},
        addEventListener(type, callback) { this.listeners[type] = callback; },
        dispatchEvent(event) { this.listeners[event.type]?.(event); },
        setAttribute(key, value) { this.attributes[key] = value; },
        setCustomValidity(value) { this.validationMessage = value; },
    });
    const low = node(min), high = node(max), lowHandle = node('0'), highHandle = node('1000');
    const limit = node(ceiling), lowSummary = node(), highSummary = node();
    const nodes = {
        '[data-range-value="min"]': low, '[data-range-value="max"]': high,
        '[data-range-handle="min"]': lowHandle, '[data-range-handle="max"]': highHandle,
        '[data-range-ceiling]': limit, '[data-range-summary-min]': lowSummary, '[data-range-summary-max]': highSummary,
    };
    const range = {
        dataset: { money: String(money), unlimited: 'No limit', error: 'Invalid range' },
        querySelector: (selector) => nodes[selector],
        style: { setProperty() {} },
    };
    const root = { lang: 'en', dataset: { tcCurrency: 'PEN' } };
    let observer;
    globalThis.document = {
        readyState: 'complete', documentElement: root,
        querySelectorAll: (selector) => selector === '[data-catalog-range]' ? [range] : [],
    };
    globalThis.MutationObserver = class {
        constructor(callback) { observer = callback; }
        observe() {}
    };
    await import('../../resources/js/catalog-discovery.js?fixture=' + ++fixture);
    return { low, high, lowHandle, highHandle, limit, lowSummary, highSummary, root, observer };
}

test('moving either price handle updates the submitted number and keeps the range ordered', async () => {
    const f = await setup();
    f.lowHandle.value = '250';
    f.lowHandle.dispatchEvent(new Event('input'));
    assert.equal(f.low.value, '500');
    f.highHandle.value = '100';
    f.highHandle.dispatchEvent(new Event('input'));
    assert.equal(f.high.value, '500');
    assert.equal(f.high.validationMessage, '');
    f.highHandle.value = '1000';
    f.highHandle.dispatchEvent(new Event('input'));
    assert.equal(f.high.value, '');
    assert.equal(f.highSummary.textContent, 'No limit');
});

test('manual inverted bounds are rejected and can be corrected', async () => {
    const f = await setup({ min: '100', max: '50' });
    assert.equal(f.high.validationMessage, 'Invalid range');
    f.high.value = '150';
    f.high.dispatchEvent(new Event('input'));
    assert.equal(f.high.validationMessage, '');
});

test('duration sliders submit whole minutes with a positive maximum', async () => {
    const f = await setup({ money: false, ceiling: '14400' });
    f.highHandle.value = '0';
    f.highHandle.dispatchEvent(new Event('input'));
    assert.equal(f.high.value, '60');
    f.low.value = '90';
    f.high.value = '180';
    f.low.dispatchEvent(new Event('input'));
    assert.equal(f.lowSummary.textContent, '1 h. 30 min.');
    assert.equal(f.highSummary.textContent, '3 h.');
    f.high.value = '1380';
    f.high.dispatchEvent(new Event('input'));
    assert.equal(f.highSummary.textContent, '23 h.');
    f.high.value = '1440';
    f.high.dispatchEvent(new Event('input'));
    assert.equal(f.highSummary.textContent, '1 d.');
    f.high.value = '14400';
    f.high.dispatchEvent(new Event('input'));
    assert.equal(f.highSummary.textContent, '10 d.');
});

test('currency refresh updates labels and slider proportions without changing input amounts', async () => {
    const f = await setup({ min: '350', max: '700' });
    f.root.dataset.tcCurrency = 'USD';
    f.low.value = '100';
    f.high.value = '200';
    f.limit.value = '571.43';
    f.observer();
    assert.equal(f.lowSummary.textContent, '$100.00');
    assert.equal(f.highSummary.textContent, '$200.00');
    assert.equal(f.low.value, '100');
    assert.ok(Math.abs(Number(f.lowHandle.value) - 175) < 1);
});
