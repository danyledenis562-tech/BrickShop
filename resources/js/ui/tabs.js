export function initTabs() {
    const tabsContainer = document.querySelector('[data-tabs]');
    if (!tabsContainer) return;

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

    document.querySelectorAll('[data-tab-trigger]').forEach((trigger) => {
        trigger.addEventListener('click', () => {
            const target = trigger.dataset.tabTrigger;
            if (target) {
                setActive(target);
                window.scrollTo({ top: tabsContainer.offsetTop - 80, behavior: 'smooth' });
            }
        });
    });
}

