export function getDeliveryPricesFromDataset(dataset) {
    try {
        const raw = dataset?.deliveryPrices;
        const parsed = raw ? JSON.parse(raw) : {};
        return {
            nova: Number(parsed?.nova ?? 100),
            courier: Number(parsed?.courier ?? 250),
            ukrposhta: Number(parsed?.ukrposhta ?? 50),
        };
    } catch {
        return { nova: 100, courier: 250, ukrposhta: 50 };
    }
}

