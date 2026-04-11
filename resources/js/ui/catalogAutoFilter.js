export function initCatalogAutoFilter() {
    const form = document.querySelector('[data-catalog-autofilter]');
    if (!form || !(form instanceof HTMLFormElement)) {
        return;
    }

    let debounceId;
    const debounceMs = 450;

    const submit = () => {
        if (typeof form.requestSubmit === 'function') {
            form.requestSubmit();
        } else {
            form.submit();
        }
    };

    form.querySelectorAll('select').forEach((el) => {
        el.addEventListener('change', () => submit());
    });

    form.querySelectorAll('input[type="checkbox"][name="in_stock"]').forEach((el) => {
        el.addEventListener('change', () => submit());
    });

    const debouncedNames = ['search', 'age', 'min_price', 'max_price'];
    debouncedNames.forEach((name) => {
        const el = form.querySelector(`input[name="${name}"]`);
        if (!el) {
            return;
        }
        el.addEventListener('input', () => {
            window.clearTimeout(debounceId);
            debounceId = window.setTimeout(submit, debounceMs);
        });
        el.addEventListener('change', () => {
            window.clearTimeout(debounceId);
            submit();
        });
    });
}
