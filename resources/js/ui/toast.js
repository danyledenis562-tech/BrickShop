export function initToast() {
    const toast = document.querySelector('[data-toast]');
    if (!toast) return;
    setTimeout(() => toast.classList.add('hidden'), 3000);
}

