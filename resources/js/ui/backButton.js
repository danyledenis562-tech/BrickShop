export function initBackButtons() {
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
}

