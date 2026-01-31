import './bootstrap';

import Alpine from 'alpinejs';

window.Alpine = Alpine;

Alpine.start();

document.addEventListener('DOMContentLoaded', () => {
    const root = document.documentElement;
    const body = document.body;
    root.classList.add('theme-fade');
    body.classList.add('theme-fade');

    const themeToggle = document.querySelector('[data-theme-toggle]');
    const themeLabel = document.querySelector('[data-theme-label]');
    const themeIcon = document.querySelector('[data-theme-icon]');
    const lightLabel = themeToggle?.dataset.themeLabelLight;
    const darkLabel = themeToggle?.dataset.themeLabelDark;
    const storedTheme = localStorage.getItem('theme');
    const prefersDark = window.matchMedia?.('(prefers-color-scheme: dark)').matches;

    const applyTheme = (theme) => {
        const isDark = theme === 'dark';
        root.setAttribute('data-theme', theme);
        localStorage.setItem('theme', theme);
        if (themeLabel) {
            themeLabel.textContent = isDark ? (darkLabel || 'Light') : (lightLabel || 'Dark');
        }
        if (themeIcon) {
            themeIcon.textContent = isDark ? '☀️' : '🌙';
        }
    };

    applyTheme(storedTheme ?? (prefersDark ? 'dark' : 'light'));

    if (themeToggle) {
        themeToggle.addEventListener('click', () => {
            applyTheme(root.dataset.theme === 'dark' ? 'light' : 'dark');
        });
    }

    const toast = document.querySelector('[data-toast]');
    if (toast) {
        setTimeout(() => toast.classList.add('hidden'), 3000);
    }

    const supportToggle = document.querySelector('[data-support-toggle]');
    const supportPopup = document.querySelector('[data-support-popup]');
    if (supportToggle && supportPopup) {
        supportToggle.addEventListener('click', () => {
            supportPopup.classList.toggle('hidden');
        });
    }

    const avatarInput = document.querySelector('[data-avatar-input]');
    if (avatarInput) {
        avatarInput.addEventListener('change', () => {
            const file = avatarInput.files?.[0];
            const preview = document.querySelector('[data-avatar-preview]');
            const fallback = document.querySelector('.profile-avatar-fallback');
            if (!file || !preview) {
                return;
            }
            const reader = new FileReader();
            reader.onload = (event) => {
                preview.src = event.target?.result;
                preview.classList.remove('hidden');
                if (fallback) {
                    fallback.classList.add('hidden');
                }
            };
            reader.readAsDataURL(file);
        });
    }

    const searchInput = document.querySelector('[data-search-input]');
    const suggestionsWrapper = document.querySelector('[data-search-suggestions]');
    const suggestionsList = document.querySelector('[data-search-items]');
    let searchTimeout;
    let lastController;

    const hideSuggestions = () => {
        if (suggestionsWrapper) {
            suggestionsWrapper.classList.add('hidden');
        }
        if (suggestionsList) {
            suggestionsList.innerHTML = '';
        }
    };

    if (searchInput && suggestionsWrapper && suggestionsList) {
        const suggestionsUrl = searchInput.dataset.searchUrl || '/search-suggestions';
        const renderItem = (item) => {
            const wrapper = document.createElement('a');
            wrapper.href = item.url;
            wrapper.className = 'flex items-center gap-3 rounded-xl px-2 py-2 hover:bg-[color:var(--lego-yellow)]';

            let image;
            if (item.image) {
                image = document.createElement('img');
                image.src = item.image;
                image.alt = item.name;
                image.className = 'h-10 w-10 rounded-lg object-cover bg-white';
            }

            const content = document.createElement('div');
            content.className = 'flex-1 text-sm';
            content.innerHTML = `
                <div class="font-semibold">${item.name}</div>
                <div class="text-xs text-[color:var(--muted)]">${item.series ?? ''}</div>
            `;

            const price = document.createElement('div');
            price.className = 'text-sm font-bold';
            price.textContent = `${item.price} грн`;

            if (image) {
                wrapper.append(image);
            }
            wrapper.append(content, price);
            return wrapper;
        };

        searchInput.addEventListener('input', () => {
            const term = searchInput.value.trim();
            clearTimeout(searchTimeout);
            if (term.length < 2) {
                hideSuggestions();
                return;
            }

            searchTimeout = setTimeout(async () => {
                if (lastController) {
                    lastController.abort();
                }
                lastController = new AbortController();
                try {
                    const response = await fetch(`${suggestionsUrl}?q=${encodeURIComponent(term)}`, {
                        signal: lastController.signal,
                    });
                    if (!response.ok) {
                        hideSuggestions();
                        return;
                    }
                    const items = await response.json();
                    suggestionsList.innerHTML = '';
                    if (!items.length) {
                        hideSuggestions();
                        return;
                    }
                    items.forEach((item) => suggestionsList.append(renderItem(item)));
                    suggestionsWrapper.classList.remove('hidden');
                } catch (error) {
                    hideSuggestions();
                }
            }, 250);
        });

        document.addEventListener('click', (event) => {
            if (!suggestionsWrapper.contains(event.target) && event.target !== searchInput) {
                hideSuggestions();
            }
        });
    }

    const tabsContainer = document.querySelector('[data-tabs]');
    if (tabsContainer) {
        const buttons = tabsContainer.querySelectorAll('[data-tab]');
        const contents = document.querySelectorAll('[data-tab-content]');

        const setActive = (name) => {
            buttons.forEach((button) => {
                button.classList.toggle('is-active', button.dataset.tab === name);
            });
            contents.forEach((content) => {
                content.classList.toggle('hidden', content.dataset.tabContent !== name);
            });
        };

        const defaultTab = tabsContainer.dataset.defaultTab || tabsContainer.querySelector('[data-tab].is-active')?.dataset.tab;
        if (defaultTab) {
            setActive(defaultTab);
        }

        buttons.forEach((button) => {
            button.addEventListener('click', () => setActive(button.dataset.tab));
        });
    }

    document.querySelectorAll('[data-back]').forEach((button) => {
        button.addEventListener('click', () => {
            const fallback = button.dataset.backFallback || '/';
            if (window.history.length > 1) {
                window.history.back();
            } else {
                window.location.href = fallback;
            }
        });
    });

    const animatedBlocks = document.querySelectorAll('[data-animate]');
    if (animatedBlocks.length) {
        const observer = new IntersectionObserver(
            (entries) => {
                entries.forEach((entry) => {
                    if (entry.isIntersecting) {
                        entry.target.classList.add('is-visible');
                        observer.unobserve(entry.target);
                    }
                });
            },
            { threshold: 0.2 }
        );

        animatedBlocks.forEach((block) => {
            block.classList.add('lego-animate');
            observer.observe(block);
        });
    }
});
