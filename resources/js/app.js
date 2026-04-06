import './bootstrap';
import './checkout';

import Alpine from 'alpinejs';
import { initTheme } from './ui/theme';
import { initToast } from './ui/toast';
import { initSupportWidget } from './ui/supportWidget';
import { initAvatarPreview } from './ui/avatarPreview';
import { initSearchSuggestions } from './ui/searchSuggestions';
import { initTabs } from './ui/tabs';
import { initBackButtons } from './ui/backButton';
import { initAnimateOnIntersect } from './ui/animateOnIntersect';
import { initProductGallery } from './ui/productGallery';
import { initProductMagnifier } from './ui/productMagnifier';
import { initCatalogAutoFilter } from './ui/catalogAutoFilter';

window.Alpine = Alpine;

Alpine.start();

document.addEventListener('DOMContentLoaded', () => {
    initTheme();
    initToast();
    initSupportWidget();
    initAvatarPreview();
    initSearchSuggestions();
    initTabs();
    initBackButtons();
    initAnimateOnIntersect();
    initProductGallery();
    initProductMagnifier();
    initCatalogAutoFilter();
});
