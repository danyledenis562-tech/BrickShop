export async function fetchNovaCities({ url, query, signal }) {
    if (!url || !query) return [];
    if (query.length < 1) return [];
    const response = await fetch(`${url}?q=${encodeURIComponent(query)}`, { signal });
    if (!response.ok) return [];
    return response.json();
}

export async function fetchNovaBranches({ url, cityRef, signal }) {
    if (!url || !cityRef) return [];
    const response = await fetch(`${url}?city_ref=${encodeURIComponent(cityRef)}`, { signal });
    if (!response.ok) return [];
    return response.json();
}

