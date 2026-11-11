/** Formats a local Date as yyyy-mm-dd. */
export function formatIsoDate(d) {
    const pad = n => String(n).padStart(2, '0');
    return d.getFullYear() + '-' + pad(d.getMonth() + 1) + '-' + pad(d.getDate());
}

export function addDays(d, days) {
    const result = new Date(d);
    result.setDate(result.getDate() + days);
    return result;
}
