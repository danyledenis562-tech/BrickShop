export function setInputLoading(inputEl, isLoading) {
    if (!inputEl) return;
    inputEl.toggleAttribute('disabled', Boolean(isLoading));
    inputEl.classList.toggle('is-loading', Boolean(isLoading));
    inputEl.setAttribute('aria-busy', Boolean(isLoading) ? 'true' : 'false');
}

