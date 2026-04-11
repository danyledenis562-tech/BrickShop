import { getDeliveryPricesFromDataset } from './checkout/deliveryPrices';
import { initCheckoutWizard } from './checkout/wizard';

// Alpine: міста (API Нової Пошти). cfg: { cityName?, cityRef? } — за замовчуванням поля НП
window.cityDropdown = function (cfg = {}) {
    const cityName = cfg.cityName ?? 'nova_city';
    const cityRef = cfg.cityRef ?? 'nova_city_ref';

    return {
        open: false,
        search: '',
        selectedName: '',
        selectedRef: '',
        items: [],
        filtered: [],
        loading: false,
        _citiesUrl: '',
        _cache: new Map(),
        _cityName: cityName,
        _cityRef: cityRef,

        init() {
            const form = this.$el.closest('[data-checkout-wizard]');
            this._citiesUrl = form?.dataset?.novaCitiesUrl || '';

            this.items = [];
            this.filtered = [];
        },

        async filter() {
            const q = (this.search || '').trim();
            if (!q) {
                this.filtered = this.items;
                return;
            }

            const key = q.toLowerCase();
            if (this._cache.has(key)) {
                this.filtered = this._cache.get(key);
                return;
            }

            if (!this._citiesUrl) {
                this.filtered = this.items;
                return;
            }

            this.loading = true;
            try {
                const resp = await fetch(`${this._citiesUrl}?q=${encodeURIComponent(q)}`);
                if (!resp.ok) {
                    this.filtered = this.items;
                    return;
                }
                const data = await resp.json();
                const mapped = (data || []).map((item) => ({
                    id: item.ref,
                    name: item.label,
                    ref: item.ref,
                }));
                this._cache.set(key, mapped);
                this.filtered = mapped;
            } catch (e) {
                this.filtered = this.items;
            } finally {
                this.loading = false;
            }
        },

        select(item) {
            this.selectedName = item.name;
            this.selectedRef = item.ref;
            this.search = item.name;
            this.open = false;

            const hiddenCity = this.$el.querySelector(`input[name="${this._cityName}"]`);
            const hiddenRef = this.$el.querySelector(`input[name="${this._cityRef}"]`);
            if (hiddenCity) hiddenCity.dispatchEvent(new Event('change', { bubbles: true }));
            if (hiddenRef) hiddenRef.dispatchEvent(new Event('change', { bubbles: true }));
        },
    };
};

// Alpine: відділення (API Нової Пошти getWarehouses). cfg: { cityRefField?, branchNameField? }
window.branchDropdown = function (cfg = {}) {
    const cityRefField = cfg.cityRefField ?? 'nova_city_ref';
    const branchNameField = cfg.branchNameField ?? 'nova_branch';

    return {
        open: false,
        search: '',
        selected: null,
        loading: false,
        items: [],
        filtered: [],
        _branchesUrl: '',
        _loadedCityRef: '',
        _cityRefField: cityRefField,
        _branchNameField: branchNameField,

        init() {
            const form = this.$el.closest('[data-checkout-wizard]');
            this._branchesUrl = form?.dataset?.novaBranchesUrl || '';

            const hiddenRef = form?.querySelector?.(`input[name="${this._cityRefField}"]`);
            hiddenRef?.addEventListener('change', () => {
                this.items = [];
                this.filtered = [];
                this.selected = null;
                this.search = '';
                this._loadedCityRef = '';
            });
        },

        async _ensureLoaded() {
            const form = this.$el.closest('[data-checkout-wizard]');
            const cityRef = form?.querySelector?.(`input[name="${this._cityRefField}"]`)?.value?.trim() || '';
            if (!cityRef || !this._branchesUrl) {
                this.items = [];
                this.filtered = [];
                return;
            }

            if (this._loadedCityRef === cityRef && this.items.length) {
                return;
            }

            this.loading = true;
            try {
                const resp = await fetch(`${this._branchesUrl}?city_ref=${encodeURIComponent(cityRef)}`);
                if (!resp.ok) {
                    this.items = [];
                    this.filtered = [];
                    return;
                }
                const data = await resp.json();
                this.items = (data || []).map((row, idx) => {
                    const label = row.label || '';
                    const [namePart, ...rest] = label.split(',');
                    return {
                        id: row.ref || String(idx),
                        name: (row.name ?? namePart ?? '').trim() || label,
                        address: (row.address ?? rest.join(',') ?? '').trim(),
                    };
                });
                this.filtered = this.items;
                this._loadedCityRef = cityRef;
            } catch (e) {
                this.items = [];
                this.filtered = [];
            } finally {
                this.loading = false;
            }
        },

        async filter() {
            await this._ensureLoaded();
            const q = this.search.toLowerCase();
            if (!q) {
                this.filtered = this.items;
                return;
            }
            this.filtered = this.items.filter((item) =>
                (item.name + ' ' + item.address).toLowerCase().includes(q),
            );
        },

        async openDropdown() {
            this.open = true;
            await this._ensureLoaded();
            this.filtered = this.items;
        },

        select(item) {
            this.selected = item;
            this.search = `${item.name}, ${item.address}`;
            this.open = false;

            const form = this.$el.closest('[data-checkout-wizard]');
            const hiddenBranch = form?.querySelector?.(`input[name="${this._branchNameField}"]`);
            if (hiddenBranch) {
                hiddenBranch.value = this.search;
                hiddenBranch.dispatchEvent(new Event('change', { bubbles: true }));
            }
        },
    };
};

// Місто для кур’єрської доставки НП
window.courierCityDropdown = function () {
    return {
        open: false,
        search: '',
        selectedName: '',
        selectedRef: '',
        items: [],
        filtered: [],
        loading: false,
        _citiesUrl: '',
        _cache: new Map(),

        init() {
            const form = this.$el.closest('[data-checkout-wizard]');
            this._citiesUrl = form?.dataset?.novaCitiesUrl || '';
            this.items = [];
            this.filtered = [];
        },

        async filter() {
            const q = (this.search || '').trim();
            if (!q) {
                this.filtered = this.items;
                return;
            }

            const key = q.toLowerCase();
            if (this._cache.has(key)) {
                this.filtered = this._cache.get(key);
                return;
            }

            if (!this._citiesUrl) {
                this.filtered = this.items;
                return;
            }

            this.loading = true;
            try {
                const resp = await fetch(`${this._citiesUrl}?q=${encodeURIComponent(q)}`);
                if (!resp.ok) {
                    this.filtered = this.items;
                    return;
                }
                const data = await resp.json();
                const mapped = (data || []).map((item) => ({
                    id: item.ref,
                    name: item.label,
                    ref: item.ref,
                }));
                this._cache.set(key, mapped);
                this.filtered = mapped;
            } catch (e) {
                this.filtered = this.items;
            } finally {
                this.loading = false;
            }
        },

        select(item) {
            this.selectedName = item.name;
            this.selectedRef = item.ref;
            this.search = item.name;
            this.open = false;

            const hiddenRef = this.$el.querySelector('input[name="courier_city_ref"]');
            if (hiddenRef) hiddenRef.dispatchEvent(new Event('change', { bubbles: true }));
        },
    };
};

// Вулиці для кур’єрської доставки НП
window.streetDropdown = function () {
    return {
        open: false,
        search: '',
        selectedName: '',
        loading: false,
        items: [],
        filtered: [],
        _streetsUrl: '',
        _loadedKey: '',

        init() {
            const form = this.$el.closest('[data-checkout-wizard]');
            this._streetsUrl = form?.dataset?.novaStreetsUrl || '';

            const cityRefInput = form?.querySelector?.('input[name="courier_city_ref"]');
            cityRefInput?.addEventListener('change', () => {
                this.items = [];
                this.filtered = [];
                this.selectedName = '';
                this.search = '';
                this._loadedKey = '';
            });
        },

        async _ensureLoaded() {
            const form = this.$el.closest('[data-checkout-wizard]');
            const cityRef = form?.querySelector?.('input[name="courier_city_ref"]')?.value?.trim() || '';
            const q = (this.search || '').trim();
            if (!cityRef || !this._streetsUrl || !q) {
                this.items = [];
                this.filtered = [];
                return;
            }

            const key = cityRef + '|' + q.toLowerCase();
            if (this._loadedKey === key && this.items.length) {
                return;
            }

            this.loading = true;
            try {
                const params = new URLSearchParams({ city_ref: cityRef, q });
                const resp = await fetch(`${this._streetsUrl}?${params.toString()}`);
                if (!resp.ok) {
                    this.items = [];
                    this.filtered = [];
                    return;
                }
                const data = await resp.json();
                this.items = (data || []).map((row, idx) => ({
                    id: row.ref || String(idx),
                    name: row.label || '',
                }));
                this.filtered = this.items;
                this._loadedKey = key;
            } catch (e) {
                this.items = [];
                this.filtered = [];
            } finally {
                this.loading = false;
            }
        },

        async filter() {
            await this._ensureLoaded();
            const q = this.search.toLowerCase();
            if (!q) {
                this.filtered = this.items;
                return;
            }
            this.filtered = this.items.filter((item) => item.name.toLowerCase().includes(q));
        },

        async openDropdown() {
            this.open = true;
            await this._ensureLoaded();
            this.filtered = this.items;
        },

        select(item) {
            this.selectedName = item.name;
            this.search = item.name;
            this.open = false;
        },
    };
};

// Ініціалізація checkout‑wizard
document.addEventListener('DOMContentLoaded', () => {
    const checkoutWizard = document.querySelector('[data-checkout-wizard]');
    if (!checkoutWizard) return;

    const subtotal = parseFloat(checkoutWizard.dataset.subtotal ?? '0') || 0;
    const discount = parseFloat(checkoutWizard.dataset.discount ?? '0') || 0;
    const deliveryPrices = getDeliveryPricesFromDataset(checkoutWizard.dataset);

    initCheckoutWizard(checkoutWizard, { deliveryPrices, subtotal, discount });
});