/**
 * Interactive product gallery: thumbnails swap the main image with a short fade.
 * Expects [data-product-gallery], [data-gallery-main], [data-gallery-thumb] with data-gallery-src / data-gallery-alt.
 */
export function initProductGallery() {
    const root = document.querySelector('[data-product-gallery]');
    if (!root) {
        return;
    }

    const mainImg = root.querySelector('[data-gallery-main]');
    const thumbs = [...root.querySelectorAll('[data-gallery-thumb]')];
    if (!mainImg || thumbs.length === 0) {
        return;
    }

    const setActive = (activeBtn) => {
        thumbs.forEach((btn) => {
            const on = btn === activeBtn;
            btn.classList.toggle('is-active', on);
            btn.setAttribute('aria-selected', on ? 'true' : 'false');
            btn.setAttribute('tabindex', on ? '0' : '-1');
        });
    };

    const swapMain = (url, alt, activeBtn) => {
        if (!url) {
            return;
        }
        const current = mainImg.getAttribute('src') || mainImg.src;
        if (current === url) {
            setActive(activeBtn);
            return;
        }

        mainImg.classList.add('is-fading');

        const finish = () => {
            mainImg.removeEventListener('transitionend', onEnd);
            mainImg.src = url;
            if (alt) {
                mainImg.alt = alt;
            }
            requestAnimationFrame(() => {
                mainImg.classList.remove('is-fading');
                setActive(activeBtn);
            });
        };

        const onEnd = (e) => {
            if (e.propertyName !== 'opacity') {
                return;
            }
            finish();
        };

        mainImg.addEventListener('transitionend', onEnd);
        window.setTimeout(() => {
            if (mainImg.classList.contains('is-fading')) {
                mainImg.removeEventListener('transitionend', onEnd);
                finish();
            }
        }, 400);
    };

    thumbs.forEach((btn) => {
        btn.addEventListener('click', () => {
            swapMain(btn.getAttribute('data-gallery-src'), btn.getAttribute('data-gallery-alt') || '', btn);
        });
    });

    let idx = Math.max(
        0,
        thumbs.findIndex((t) => t.classList.contains('is-active')),
    );

    if (thumbs.length > 1) {
        root.addEventListener('keydown', (e) => {
            if (e.key !== 'ArrowRight' && e.key !== 'ArrowLeft') {
                return;
            }
            e.preventDefault();
            idx = e.key === 'ArrowRight' ? (idx + 1) % thumbs.length : (idx - 1 + thumbs.length) % thumbs.length;
            thumbs[idx].click();
            thumbs[idx].focus();
        });
    }

    setActive(thumbs[idx] ?? thumbs[0]);
}
