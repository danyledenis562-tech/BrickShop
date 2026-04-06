export function initProductMagnifier() {
    const magnifier = document.getElementById('product-magnifier');
    const lens = document.getElementById('magnifier-lens');
    const magnifierImg = magnifier?.querySelector('[data-magnifier-source]');
    const zoomFactor = 2.2;

    if (!magnifier || !lens || !magnifierImg?.src) return;

    magnifier.addEventListener('mouseenter', () => magnifier.classList.add('is-active'));
    magnifier.addEventListener('mouseleave', () => {
        magnifier.classList.remove('is-active');
        lens.style.backgroundImage = '';
    });

    magnifier.addEventListener('mousemove', (e) => {
        const rect = magnifier.getBoundingClientRect();
        const x = e.clientX - rect.left;
        const y = e.clientY - rect.top;
        const w = rect.width;
        const h = rect.height;

        const lensSize = 120;
        let left = x - lensSize / 2;
        let top = y - lensSize / 2;
        left = Math.max(0, Math.min(left, w - lensSize));
        top = Math.max(0, Math.min(top, h - lensSize));

        lens.style.left = `${left}px`;
        lens.style.top = `${top}px`;

        const bgPosX = lensSize / 2 - x * zoomFactor;
        const bgPosY = lensSize / 2 - y * zoomFactor;

        lens.style.backgroundImage = `url(${magnifierImg.src})`;
        lens.style.backgroundSize = `${w * zoomFactor}px ${h * zoomFactor}px`;
        lens.style.backgroundPosition = `${bgPosX}px ${bgPosY}px`;
    });
}

