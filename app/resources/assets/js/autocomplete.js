import TomSelect from 'tom-select';

/**
 * Remote search box built on Tom Select. Picking a suggestion calls onSelect(item) and resets the box.
 *
 * autocomplete(selectElement, {
 *   url: base url, the query is sent as ?q=...
 *   render(item, escape): html string for a suggestion
 *   onSelect(item)
 * })
 * Returns the TomSelect instance (e.g. to call .focus()).
 */
export default function autocomplete(select, {url, render, onSelect, minLength = 1, delay = 150}) {
    let controller = null;
    let failed = false;
    return new TomSelect(select, {
        maxItems: 1,
        valueField: 'id',
        labelField: 'id',
        searchField: [],
        create: false,
        persist: false,
        openOnFocus: false,
        loadThrottle: delay,
        shouldLoad: query => query.trim().length >= minLength,
        // The server already filters the suggestions.
        score: () => () => 1,
        load(query, callback) {
            // Cancel the previous request: only the latest query may update the suggestions.
            if (controller) controller.abort();
            const own = controller = new AbortController();
            failed = false;
            fetch(`${url}?q=${encodeURIComponent(query.trim())}`, {headers: {'Accept': 'application/json'}, signal: own.signal})
                .then(response => {
                    if (!response.ok) throw new Error(`HTTP ${response.status}`);
                    return response.json();
                })
                .then(items => {
                    this.clearOptions();
                    callback(items);
                })
                .catch(error => {
                    // An aborted request is superseded by a newer one that will report its own result.
                    if (error.name !== 'AbortError') failed = true;
                    callback();
                });
        },
        render: {
            option: (item, escape) => `<div>${render(item, escape)}</div>`,
            item: () => '<div></div>',
            no_results: () => (failed
                ? '<div class="no-results">Search failed, please try again.</div>'
                : '<div class="no-results">Nothing found.</div>'),
        },
        onItemAdd(value) {
            const item = this.options[value];
            this.clear(true);
            this.clearOptions();
            this.close();
            onSelect(item);
        },
    });
}
