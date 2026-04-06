import { fetchNovaCities } from './novaApi';
import { setInputLoading } from './uiStates';

// Фолбек-міста, якщо API нічого не віддає / недоступне
const FALLBACK_CITIES = [
    { ref: 'city_odessa', label: 'Одеса', area: 'Одеська область', district: 'Приморський район' },
    { ref: 'city_odessa_kiev', label: 'Одеса (Київський район)', area: 'Одеська область', district: 'Київський район' },
    { ref: 'city_odessa_suv', label: 'Одеса (Суворовський район)', area: 'Одеська область', district: 'Суворовський район' },
    { ref: 'city_kyiv', label: 'Київ', area: 'Київська область', district: '' },
    { ref: 'city_lviv', label: 'Львів', area: 'Львівська область', district: '' },
];

export function initNovaCityTypeahead(checkoutWizard, { onCitySelected } = {}) {
    const novaCitiesUrl = checkoutWizard?.dataset?.novaCitiesUrl;
    const novaCityInput = checkoutWizard?.querySelector?.('[data-nova-city-input]');
    const novaCityRefInput = checkoutWizard?.querySelector?.('[data-nova-city-ref]');
    const novaCityList = checkoutWizard?.querySelector?.('[data-nova-city-list]');
    const novaBranchInput = checkoutWizard?.querySelector?.('[data-np-branch-input]') || checkoutWizard?.querySelector?.('[data-nova-branch-input]');

    if (!checkoutWizard || !novaCitiesUrl || !novaCityInput || !novaCityList || !novaCityRefInput) {
        return { ensureCityRef: async () => null };
    }

    const state = {
        filtered: [],
        activeIndex: -1,
    };

    const hideList = () => {
        novaCityList.classList.add('hidden');
        novaCityInput.setAttribute('aria-expanded', 'false');
    };

    const showList = () => {
        novaCityList.classList.remove('hidden');
        novaCityInput.setAttribute('aria-expanded', 'true');
    };

    const announce = (message) => {
        const live = checkoutWizard.querySelector('[data-checkout-live]');
        if (!live) return;
        live.textContent = message;
    };

    const setActive = (index) => {
        state.activeIndex = index;
        const optionEls = Array.from(novaCityList.querySelectorAll('[role="option"]'));
        optionEls.forEach((el, idx) => {
            el.classList.toggle('is-active', idx === index);
            el.setAttribute('aria-selected', idx === index ? 'true' : 'false');
        });
    };

    const selectCity = (opt) => {
        novaCityInput.value = opt.label;
        novaCityRefInput.value = opt.ref || '';
        if (novaBranchInput) novaBranchInput.value = '';
        hideList();
        onCitySelected?.(opt);
    };

    const render = (options, query) => {
        novaCityList.innerHTML = '';
        if (!options.length) {
            novaCityList.innerHTML = `
                <div class="checkout-suggest-message">
                    <div>Нічого не знайдено. Спробуйте інший запит</div>
                </div>
            `;
            showList();
            announce('Місто не знайдено');
            return;
        }

        state.filtered = options;
        const fragment = document.createDocumentFragment();
        options.forEach((opt, idx) => {
            const optionEl = document.createElement('div');
            optionEl.className = 'checkout-suggest-item';
            optionEl.setAttribute('role', 'option');
            optionEl.setAttribute('aria-selected', 'false');
            optionEl.tabIndex = -1;
            const subtitle = [opt.area, opt.district].filter(Boolean).join(', ');
            optionEl.innerHTML = `
                <span class="checkout-suggest-item-title">${opt.label}</span>
                ${subtitle ? `<span class="checkout-suggest-item-subtitle">${subtitle}</span>` : ''}
            `;
            optionEl.addEventListener('mouseenter', () => setActive(idx));
            optionEl.addEventListener('click', () => selectCity(opt));
            fragment.append(optionEl);
        });
        novaCityList.append(fragment);
        showList();
        state.activeIndex = -1;
    };

    const renderError = () => {
        novaCityList.innerHTML = `
            <div class="checkout-suggest-message">
                <div class="text-red-600">Не вдалося завантажити міста. Перевірте інтернет і спробуйте ще раз.</div>
            </div>
        `;
        showList();
        announce('Помилка завантаження міст');
    };

    let abort = null;
    const update = async (query) => {
        try {
            if (abort) abort.abort();
            abort = new AbortController();
            setInputLoading(novaCityInput, true);

            let cities = [];
            try {
                cities = await fetchNovaCities({ url: novaCitiesUrl, query, signal: abort.signal });
            } catch {
                // ігноруємо помилку, підемо у фолбек
            }

            if (!Array.isArray(cities) || !cities.length) {
                const q = query.toLowerCase();
                const fallbackFiltered = FALLBACK_CITIES.filter((item) =>
                    (item.label || '').toLowerCase().includes(q),
                ).slice(0, 12);
                render(fallbackFiltered, query);
                return;
            }

            const filtered = cities
                .filter((item) => (item.label || '').toLowerCase().includes(query.toLowerCase()))
                .slice(0, 12);
            render(filtered, query);
        } catch (e) {
            if (e?.name === 'AbortError') {
                return;
            }
            renderError();
        } finally {
            setInputLoading(novaCityInput, false);
        }
    };

    novaCityInput.setAttribute('aria-expanded', 'false');

    novaCityInput.addEventListener('input', () => {
        const value = novaCityInput.value.trim();
        novaCityRefInput.value = '';
        if (novaBranchInput) novaBranchInput.value = '';
        if (!value) {
            hideList();
            return;
        }
        update(value);
    });

    novaCityInput.addEventListener('keydown', (e) => {
        const options = Array.from(novaCityList.querySelectorAll('[role="option"]'));
        const open = !novaCityList.classList.contains('hidden') && options.length > 0;
        if (!open) return;

        if (e.key === 'ArrowDown') {
            e.preventDefault();
            const next = state.activeIndex + 1;
            setActive(next >= options.length ? 0 : next);
        } else if (e.key === 'ArrowUp') {
            e.preventDefault();
            const prev = state.activeIndex - 1;
            setActive(prev < 0 ? options.length - 1 : prev);
        } else if (e.key === 'Enter') {
            e.preventDefault();
            const idx = state.activeIndex;
            if (idx >= 0 && idx < state.filtered.length) {
                selectCity(state.filtered[idx]);
            }
        } else if (e.key === 'Escape') {
            hideList();
        }
    });

    document.addEventListener('click', (e) => {
        const target = e.target;
        if (!(target instanceof Element)) return;
        if (!target.closest('[data-nova-city-input]') && !target.closest('[data-nova-city-list]')) {
            hideList();
        }
    });

    const ensureCityRef = async () => {
        if (novaCityRefInput.value.trim()) {
            return { label: novaCityInput.value.trim(), ref: novaCityRefInput.value.trim() };
        }
        const cityName = novaCityInput.value.trim();
        if (cityName.length < 1) return null;

        const controller = new AbortController();
        try {
            const cities = await fetchNovaCities({ url: novaCitiesUrl, query: cityName, signal: controller.signal });
            if (!cities.length) return null;
            const exact = cities.find((c) => (c.label || '').toLowerCase() === cityName.toLowerCase());
            const selected = exact || cities[0];
            novaCityRefInput.value = selected.ref || '';
            return selected;
        } catch {
            return null;
        }
    };

    return { ensureCityRef };
}

