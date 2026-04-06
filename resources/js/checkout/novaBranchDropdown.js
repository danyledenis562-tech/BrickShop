import { fetchNovaBranches } from './novaApi';
import { setInputLoading } from './uiStates';

export function initNovaBranchDropdown(checkoutWizard, { ensureCityRef, onBranchSelected } = {}) {
    const novaBranchesUrl = checkoutWizard?.dataset?.novaBranchesUrl;
    const npBranchDropdown = checkoutWizard?.querySelector?.('[data-np-branch-dropdown]');
    const npBranchInput = checkoutWizard?.querySelector?.('[data-np-branch-input]');
    const npBranchMenu = checkoutWizard?.querySelector?.('[data-np-branch-menu]');

    if (!checkoutWizard || !novaBranchesUrl || !npBranchDropdown || !npBranchInput || !npBranchMenu) {
        return;
    }

    const cache = new Map(); // cityRef -> branches
    let abort = null;
    let activeIndex = -1;
    let filtered = [];

    const open = () => {
        npBranchMenu.classList.remove('hidden');
        npBranchInput.setAttribute('aria-expanded', 'true');
    };
    const close = () => {
        npBranchMenu.classList.add('hidden');
        npBranchInput.setAttribute('aria-expanded', 'false');
        activeIndex = -1;
    };

    const renderMessage = (type) => {
        if (type === 'loading') {
            npBranchMenu.innerHTML = `
                <div class="np-dropdown-message">
                    <span class="checkout-spinner" aria-hidden="true"></span>
                    <span>Завантаження відділень...</span>
                </div>
            `;
            return;
        }
        if (type === 'empty') {
            npBranchMenu.innerHTML = `
                <div class="np-dropdown-message">
                    <span>Нічого не знайдено</span>
                </div>
            `;
            return;
        }
        npBranchMenu.innerHTML = `
            <div class="np-dropdown-message">
                <span>Спочатку оберіть місто</span>
            </div>
        `;
    };

    const renderError = () => {
        npBranchMenu.innerHTML = `
            <div class="np-dropdown-message">
                <span class="text-red-600">Не вдалося завантажити відділення. Спробуйте ще раз.</span>
            </div>
        `;
    };

    const announce = (message) => {
        const live = checkoutWizard.querySelector('[data-checkout-live]');
        if (!live) return;
        live.textContent = message;
    };

    const setActive = (index) => {
        activeIndex = index;
        const items = Array.from(npBranchMenu.querySelectorAll('[data-np-branch-item]'));
        items.forEach((el, idx) => {
            el.classList.toggle('is-active', idx === activeIndex);
            el.setAttribute('aria-selected', idx === activeIndex ? 'true' : 'false');
        });
    };

    const renderList = () => {
        npBranchMenu.innerHTML = '';
        if (!filtered.length) {
            renderMessage('empty');
            return;
        }

        const fragment = document.createDocumentFragment();

        filtered.slice(0, 60).forEach((opt, idx) => {
            // Розбиваємо рядок на об'єкт: name + address
            const raw = opt.label || '';
            const [namePart, ...rest] = raw.split(',');
            const name = (opt.name ?? namePart ?? '').trim();
            const address = (opt.address ?? rest.join(',') ?? '').trim();

            const item = {
                id: opt.id ?? opt.ref ?? String(idx),
                name: name || raw,
                address: address,
            };

            const btn = document.createElement('button');
            btn.type = 'button';
            btn.className = 'np-branch-card';
            btn.dataset.npBranchItem = '1';
            btn.setAttribute('role', 'option');
            btn.setAttribute('aria-selected', 'false');

            btn.innerHTML = `
                <div class="np-branch-card-name">📦 ${item.name}</div>
                <div class="np-branch-card-address">${item.address}</div>
            `;

            btn.addEventListener('mouseenter', () => setActive(idx));
            btn.addEventListener('click', () => {
                // Вставляємо обране відділення в інпут
                npBranchInput.value = item.address
                    ? `${item.name}, ${item.address}`
                    : item.name;

                close();

                // Повертаємо об'єкт з чіткою структурою
                onBranchSelected?.({
                    ...opt,
                    id: item.id,
                    name: item.name,
                    address: item.address,
                });
            });

            fragment.append(btn);
        });

        npBranchMenu.append(fragment);
        setActive(-1);
    };

    const filter = (branches) => {
        const q = (npBranchInput?.value ?? '').trim().toLowerCase();
        if (!q) {
            filtered = branches.slice();
        } else {
            filtered = branches.filter((b) => {
                const raw = b.label || '';
                // Фільтруємо по всьому рядку (назва + адреса)
                return raw.toLowerCase().includes(q);
            });
        }
        renderList();
    };

    const ensureLoaded = async () => {
        const city = (await ensureCityRef?.()) ?? null;
        const cityRef = city?.ref || '';
        if (!cityRef) {
            renderMessage('city_missing');
            announce('Спочатку оберіть місто');
            return null;
        }

        if (cache.has(cityRef)) return cache.get(cityRef);

        try {
            if (abort) abort.abort();
            abort = new AbortController();
            renderMessage('loading');
            setInputLoading(npBranchInput, true);
            const branches = await fetchNovaBranches({ url: novaBranchesUrl, cityRef, signal: abort.signal });
            cache.set(cityRef, branches);
            return branches;
        } catch (e) {
            if (e?.name === 'AbortError') {
                return null;
            }
            renderError();
            announce('Помилка завантаження відділень');
            return null;
        } finally {
            setInputLoading(npBranchInput, false);
        }
    };

    const openAndRender = async () => {
        open();
        const branches = await ensureLoaded();
        if (!branches) return;
        if (!branches.length) {
            renderMessage('empty');
            announce('Відділення не знайдено');
            return;
        }
        filter(branches);
    };

    npBranchInput.addEventListener('focus', openAndRender);
    npBranchInput.addEventListener('input', async () => {
        const branches = await ensureLoaded();
        if (!branches) return;
        filter(branches);
        open();
    });

    npBranchInput.addEventListener('keydown', (e) => {
        const items = Array.from(npBranchMenu.querySelectorAll('[data-np-branch-item]'));
        const isOpen = !npBranchMenu.classList.contains('hidden');
        if (!isOpen) return;

        if (e.key === 'Escape') {
            e.preventDefault();
            close();
            return;
        }
        if (!items.length) return;

        if (e.key === 'ArrowDown') {
            e.preventDefault();
            const next = activeIndex + 1;
            setActive(next >= items.length ? 0 : next);
        } else if (e.key === 'ArrowUp') {
            e.preventDefault();
            const prev = activeIndex - 1;
            setActive(prev < 0 ? items.length - 1 : prev);
        } else if (e.key === 'Enter') {
            if (activeIndex >= 0 && activeIndex < filtered.length) {
                e.preventDefault();
                const opt = filtered[activeIndex];
                npBranchInput.value = opt.label;
                close();
                onBranchSelected?.(opt);
            }
        }
    });

    document.addEventListener('click', (e) => {
        const target = e.target;
        if (!(target instanceof Element)) return;
        if (!target.closest('[data-np-branch-dropdown]')) {
            close();
        }
    });

    // If city changes, clear selection
    const novaCityInput = checkoutWizard.querySelector('[data-nova-city-input]');
    novaCityInput?.addEventListener('change', () => {
        npBranchInput.value = '';
        filtered = [];
        activeIndex = -1;
        close();
    });
}

