export function initAnimateOnIntersect() {
    const animatedBlocks = document.querySelectorAll('[data-animate]');
    if (!animatedBlocks.length) return;

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

