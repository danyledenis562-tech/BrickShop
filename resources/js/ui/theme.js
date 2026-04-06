export function initTheme() {
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
}

