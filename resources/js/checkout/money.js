export function formatMoney(value) {
    const safe = Number.isFinite(value) ? value : 0;
    return safe.toLocaleString('uk-UA', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
}

