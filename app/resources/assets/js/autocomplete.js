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
    let latest = 0;
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
            const request = ++latest;
            fetch(`${url}?q=${encodeURIComponent(query.trim())}`, {headers: {'Accept': 'application/json'}})
                .then(response => (response.ok ? response.json() : []))
                .then(items => {
                    if (request === latest) {
                        this.clearOptions();
                        callback(items);
                    } else {
                        callback();
                    }
                })
                .catch(() => callback());
        },
        render: {
            option: (item, escape) => `<div>${render(item, escape)}</div>`,
            item: () => '<div></div>',
            no_results: () => '<div class="no-results">Nothing found.</div>',
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
