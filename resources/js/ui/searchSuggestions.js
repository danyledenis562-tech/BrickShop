export function initSearchSuggestions() {
    const searchInput = document.querySelector('[data-search-input]');
    const suggestionsWrapper = document.querySelector('[data-search-suggestions]');
    const suggestionsList = document.querySelector('[data-search-items]');
    if (!searchInput || !suggestionsWrapper || !suggestionsList) return;

    const suggestionsUrl = searchInput.dataset.searchUrl || '/search-suggestions';
    let searchTimeout;
    let lastController;

    const hideSuggestions = () => {
        suggestionsWrapper.classList.add('hidden');
        suggestionsList.innerHTML = '';
    };

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

        if (image) wrapper.append(image);
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
            if (lastController) lastController.abort();
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
            } catch {
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

