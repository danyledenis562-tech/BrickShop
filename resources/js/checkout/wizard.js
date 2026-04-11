import { formatMoney } from './money';

export function initCheckoutWizard(checkoutWizard, options) {
    if (!checkoutWizard) return;

    const {
        deliveryPrices,
        subtotal,
        discount,
        onDeliveryChanged = () => {},
        onPaymentChanged = () => {},
        onAfterStateSync = () => {},
    } = options ?? {};

    const stages = Array.from(checkoutWizard.querySelectorAll('[data-checkout-step]'));
    const navButtons = Array.from(checkoutWizard.querySelectorAll('[data-step-nav]'));
    const deliveryRadios = Array.from(checkoutWizard.querySelectorAll('input[name="delivery_type"][data-delivery-radio]'));
    const deliveryRadioCards = Array.from(checkoutWizard.querySelectorAll('[data-delivery-radio-card]'));
    const deliveryBlocks = Array.from(checkoutWizard.querySelectorAll('[data-delivery-block]'));
    const hiddenCityInput = checkoutWizard.querySelector('[data-delivery-city-hidden]');
    const hiddenAddressInput = checkoutWizard.querySelector('[data-delivery-address-hidden]');
    const shippingPriceBadge = document.querySelector('[data-checkout-shipping-price]');
    const paymentRadioCards = Array.from(checkoutWizard.querySelectorAll('[data-payment-radio-card]'));
    const paymentCardSection = document.querySelector('[data-payment-card]');
    const paymentPreview = checkoutWizard.querySelector('[data-checkout-preview-payment]');
    const deliveryPreview = checkoutWizard.querySelector('[data-checkout-preview-delivery]');
    const deliveryPricePreview = checkoutWizard.querySelector('[data-checkout-preview-delivery-price]');
    const totalEl = checkoutWizard.querySelector('[data-checkout-total]');
    const bonusToSpendInput = checkoutWizard.querySelector('input[name="bonus_to_spend"]');

    let currentStep = 1;
    const messages = {
        cardNumberInvalid: checkoutWizard.dataset.msgCardNumberInvalid || 'Invalid card number',
        cardExpiryInvalid: checkoutWizard.dataset.msgCardExpiryInvalid || 'Invalid expiry date',
        cardCvvInvalid: checkoutWizard.dataset.msgCardCvvInvalid || 'Invalid CVV',
        novaBranchRequired: checkoutWizard.dataset.msgNovaBranchRequired || 'Select branch',
        courierCityRequired: checkoutWizard.dataset.msgCourierCityRequired || 'Select city',
        courierStreetRequired: checkoutWizard.dataset.msgCourierStreetRequired || 'Select street',
        courierHouseRequired: checkoutWizard.dataset.msgCourierHouseRequired || 'Enter house number',
        ukrposhtaCityRequired: checkoutWizard.dataset.msgUkrposhtaCityRequired || 'Select city',
        ukrposhtaBranchRequired: checkoutWizard.dataset.msgUkrposhtaBranchRequired || 'Select branch',
    };

    const getActiveStage = () => stages.find((stage) => Number(stage.dataset.checkoutStep) === currentStep);

    const clearFieldErrors = () => {
        checkoutWizard
            .querySelectorAll('[data-field-error]')
            .forEach((el) => {
                el.textContent = '';
            });
        checkoutWizard
            .querySelectorAll('.lego-input-error')
            .forEach((el) => el.classList.remove('lego-input-error'));
    };

    const setFieldError = (name, message) => {
        const input = checkoutWizard.querySelector(`.lego-input[name="${name}"]`);
        const errorEl = checkoutWizard.querySelector(`[data-field-error="${name}"]`);
        if (input) input.classList.add('lego-input-error');
        if (errorEl) errorEl.textContent = message;
    };

    const validateCardFields = () => {
        const paymentType = checkoutWizard.querySelector('input[name="payment_type"]:checked')?.value;
        if (paymentType !== 'card') return true;

        let ok = true;
        const num = checkoutWizard.querySelector('input[name="card_number"]')?.value.replace(/\s+/g, '') ?? '';
        const exp = checkoutWizard.querySelector('input[name="card_expiry"]')?.value ?? '';
        const cvv = checkoutWizard.querySelector('input[name="card_cvv"]')?.value ?? '';

        if (!/^\d{16}$/.test(num)) {
            setFieldError('card_number', messages.cardNumberInvalid);
            ok = false;
        }

        if (!/^(0[1-9]|1[0-2])\/\d{2}$/.test(exp)) {
            setFieldError('card_expiry', messages.cardExpiryInvalid);
            ok = false;
        }

        if (!/^\d{3}$/.test(cvv)) {
            setFieldError('card_cvv', messages.cardCvvInvalid);
            ok = false;
        }

        return ok;
    };

    const validateStage = (stageEl) => {
        if (!stageEl) return true;

        clearFieldErrors();

        const step = Number(stageEl.dataset.checkoutStep);

        if (step === 2) {
            if (!validateCardFields()) return false;
        }

        // Базова HTML5‑валідація
        const fields = Array.from(stageEl.querySelectorAll('input, select, textarea'));
        const invalid = fields.find((el) => typeof el.checkValidity === 'function' && !el.checkValidity());
        if (invalid) {
            if (typeof invalid.reportValidity === 'function') invalid.reportValidity();
            return false;
        }

        // Додаткова логіка для кроку доставки
        if (step === 1) {
            const checkedDelivery = checkoutWizard.querySelector('input[name="delivery_type"]:checked');
            const selectedDeliveryType = checkedDelivery?.value ?? 'nova';

            if (selectedDeliveryType === 'nova') {
                const branchId = checkoutWizard.querySelector('input[name="branch_id"]')?.value?.trim() || '';
                if (!branchId) {
                    const errorEl = checkoutWizard.querySelector('[data-field-error="nova_branch"]');
                    if (errorEl) errorEl.textContent = messages.novaBranchRequired;
                    return false;
                }
            }

            if (selectedDeliveryType === 'courier') {
                let ok = true;
                const city = checkoutWizard.querySelector('input[name="courier_city"]')?.value?.trim() || '';
                const street = checkoutWizard.querySelector('input[name="courier_street"]')?.value?.trim() || '';
                const house = checkoutWizard.querySelector('input[name="courier_house"]')?.value?.trim() || '';

                if (!city) {
                    const el = checkoutWizard.querySelector('[data-field-error="courier_city"]');
                    if (el) el.textContent = messages.courierCityRequired;
                    ok = false;
                }
                if (!street) {
                    const el = checkoutWizard.querySelector('[data-field-error="courier_street"]');
                    if (el) el.textContent = messages.courierStreetRequired;
                    ok = false;
                }
                if (!house) {
                    const el = checkoutWizard.querySelector('[data-field-error="courier_house"]');
                    if (el) el.textContent = messages.courierHouseRequired;
                    ok = false;
                }

                if (!ok) return false;
            }

            if (selectedDeliveryType === 'ukrposhta') {
                let ok = true;
                const city = checkoutWizard.querySelector('input[name="ukrposhta_city"]')?.value?.trim() || '';
                const branch = checkoutWizard.querySelector('input[name="ukrposhta_branch"]')?.value?.trim() || '';
                if (!city) {
                    const el = checkoutWizard.querySelector('[data-field-error="ukrposhta_city"]');
                    if (el) el.textContent = messages.ukrposhtaCityRequired;
                    ok = false;
                }
                if (!branch) {
                    const el = checkoutWizard.querySelector('[data-field-error="ukrposhta_branch"]');
                    if (el) el.textContent = messages.ukrposhtaBranchRequired;
                    ok = false;
                }
                if (!ok) return false;
            }
        }

        return true;
    };

    const showStep = (step) => {
        currentStep = Math.max(1, Math.min(step, stages.length));
        stages.forEach((stage) => {
            stage.classList.toggle('is-active', Number(stage.dataset.checkoutStep) === currentStep);
        });
        navButtons.forEach((button) => {
            const stepNumber = Number(button.dataset.stepNav);
            button.classList.toggle('is-active', stepNumber === currentStep);
            button.classList.toggle('is-done', stepNumber < currentStep);
        });
    };

    const updateRadioCardsSelection = () => {
        deliveryRadioCards.forEach((card) => {
            const input = card.querySelector('input[name="delivery_type"]');
            card.classList.toggle('is-selected', Boolean(input?.checked));
        });
        paymentRadioCards.forEach((card) => {
            const input = card.querySelector('input[name="payment_type"]');
            card.classList.toggle('is-selected', Boolean(input?.checked));
        });
    };

    const updatePaymentCardVisibility = () => {
        if (!paymentCardSection) return;
        const selected = checkoutWizard.querySelector('input[name="payment_type"]:checked')?.value;
        paymentCardSection.classList.toggle('hidden', selected !== 'card');
    };

    const updateDeliveryBlocks = () => {
        const checkedDelivery = checkoutWizard.querySelector('input[name="delivery_type"]:checked');
        const selectedDeliveryType = checkedDelivery?.value ?? 'nova';

        deliveryBlocks.forEach((block) => {
            const isActive = block.dataset.deliveryBlock === selectedDeliveryType;
            block.classList.toggle('hidden', !isActive);

            block.querySelectorAll('input, select').forEach((field) => {
                if (field.name === 'full_name' || field.name === 'phone') return;

                const isRefField = field.name.endsWith('_ref');
                if (field.name.includes(selectedDeliveryType) && isActive && !isRefField) {
                    field.required = true;
                } else if (field.name.startsWith('nova_') || field.name.startsWith('courier_') || field.name.startsWith('ukrposhta_')) {
                    field.required = false;
                }
            });
        });

        const cityField = checkoutWizard.querySelector(`[data-delivery-city-field="${selectedDeliveryType}"]`);
        if (hiddenCityInput && cityField) {
            hiddenCityInput.value = cityField.value.trim();
        }

        if (hiddenAddressInput) {
            if (selectedDeliveryType === 'nova') {
                const branch = checkoutWizard.querySelector('input[name="nova_branch"]')?.value?.trim() || '';
                hiddenAddressInput.value = branch ? `Відділення НП: ${branch}` : '';
            } else if (selectedDeliveryType === 'ukrposhta') {
                const branch = checkoutWizard.querySelector('input[name="ukrposhta_branch"]')?.value?.trim() || '';
                hiddenAddressInput.value = branch ? `Відділення Укрпошти: ${branch}` : '';
            } else {
                const city = checkoutWizard.querySelector('input[name="courier_city"]')?.value?.trim() || '';
                const street = checkoutWizard.querySelector('input[name="courier_street"]')?.value?.trim() || '';
                const house = checkoutWizard.querySelector('input[name="courier_house"]')?.value?.trim() || '';
                const apartment = checkoutWizard.querySelector('input[name="courier_apartment"]')?.value?.trim() || '';
                hiddenAddressInput.value = `${city ? city + ', ' : ''}вул. ${street}, буд. ${house}${apartment ? ', кв. ' + apartment : ''}`.trim();
            }
        }
    };

    const updateConfirmPreviewAndTotal = () => {
        const checkedDelivery = checkoutWizard.querySelector('input[name="delivery_type"]:checked');
        const selectedDeliveryType = checkedDelivery?.value ?? 'nova';
        const shippingPrice = deliveryPrices?.[selectedDeliveryType] ?? 0;

        if (deliveryPreview) {
            deliveryPreview.textContent = checkedDelivery?.closest('[data-delivery-radio-card]')?.dataset.deliveryLabel || '—';
        }

        if (paymentPreview) {
            const checkedPayment = checkoutWizard.querySelector('input[name="payment_type"]:checked');
            paymentPreview.textContent = checkedPayment?.closest('[data-payment-radio-card]')?.dataset.paymentLabel || '—';
        }

        if (deliveryPricePreview) {
            deliveryPricePreview.textContent = `${formatMoney(shippingPrice)} грн`;
        }
        if (shippingPriceBadge) {
            shippingPriceBadge.textContent = `${formatMoney(shippingPrice)} грн`;
        }

        const bonusToSpend = parseInt(bonusToSpendInput?.value ?? '0', 10) || 0;
        const totalBeforeBonus = (subtotal ?? 0) - (discount ?? 0) + shippingPrice;
        const total = Math.max(0, totalBeforeBonus - bonusToSpend);
        if (totalEl) {
            totalEl.textContent = formatMoney(total);
        }
    };

    checkoutWizard.querySelectorAll('[data-step-next]').forEach((button) => {
        button.addEventListener('click', () => {
            const activeStage = getActiveStage();
            if (!validateStage(activeStage)) return;
            showStep(currentStep + 1);
            updateConfirmPreviewAndTotal();
        });
    });

    checkoutWizard.querySelectorAll('[data-step-prev]').forEach((button) => {
        button.addEventListener('click', () => showStep(currentStep - 1));
    });

    navButtons.forEach((button) => {
        button.addEventListener('click', () => {
            showStep(Number(button.dataset.stepNav));
            updateConfirmPreviewAndTotal();
        });
    });

    deliveryRadios.forEach((radio) => {
        radio.addEventListener('change', () => {
            updateRadioCardsSelection();
            updatePaymentCardVisibility();
            updateDeliveryBlocks();
            updateConfirmPreviewAndTotal();
            onDeliveryChanged(radio.value);

            if (radio.value !== 'nova') {
                const novaBranchInput = checkoutWizard.querySelector('input[name="nova_branch"]');
                const novaCityRefInput = checkoutWizard.querySelector('input[name="nova_city_ref"]');
                if (novaBranchInput) novaBranchInput.value = '';
                if (novaCityRefInput) novaCityRefInput.value = '';
            }
            if (radio.value !== 'ukrposhta') {
                const ukrCity = checkoutWizard.querySelector('input[name="ukrposhta_city"]');
                const ukrCityRef = checkoutWizard.querySelector('input[name="ukrposhta_city_ref"]');
                const ukrBranch = checkoutWizard.querySelector('input[name="ukrposhta_branch"]');
                if (ukrCity) ukrCity.value = '';
                if (ukrCityRef) ukrCityRef.value = '';
                if (ukrBranch) ukrBranch.value = '';
            }
        });
    });

    checkoutWizard.querySelectorAll('input[name="payment_type"]').forEach((radio) => {
        radio.addEventListener('change', () => {
            updateRadioCardsSelection();
            updatePaymentCardVisibility();
            updateConfirmPreviewAndTotal();
            onPaymentChanged(radio.value);
        });
    });

    // Public sync hook for other modules (e.g., Nova branch select)
    const syncAll = () => {
        updateRadioCardsSelection();
        updatePaymentCardVisibility();
        updateDeliveryBlocks();
        updateConfirmPreviewAndTotal();
        onAfterStateSync();
    };

    syncAll();
    showStep(1);
    updateConfirmPreviewAndTotal();

    return { syncAll, updateDeliveryBlocks, updateConfirmPreviewAndTotal };
}

