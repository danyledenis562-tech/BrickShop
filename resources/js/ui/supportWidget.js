export function initSupportWidget() {
    const supportToggle = document.querySelector('[data-support-toggle]');
    const supportPopup = document.querySelector('[data-support-popup]');
    if (!supportToggle || !supportPopup) return;
    supportToggle.addEventListener('click', () => {
        supportPopup.classList.toggle('hidden');
    });
}

