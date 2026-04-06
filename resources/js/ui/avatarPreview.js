export function initAvatarPreview() {
    const avatarInput = document.querySelector('[data-avatar-input]');
    if (!avatarInput) return;

    avatarInput.addEventListener('change', () => {
        const file = avatarInput.files?.[0];
        const preview = document.querySelector('[data-avatar-preview]');
        const fallback = document.querySelector('.profile-avatar-fallback');
        if (!file || !preview) return;

        const reader = new FileReader();
        reader.onload = (event) => {
            preview.src = event.target?.result;
            preview.classList.remove('hidden');
            if (fallback) fallback.classList.add('hidden');
        };
        reader.readAsDataURL(file);
    });
}

