/**
 * Minimal remote autocomplete (replaces typeahead.js + Bloodhound).
 *
 * autocomplete(input, {
 *   url: base url, the query is sent as ?q=...
 *   display(item): string put in the input
 *   render(item, escape): html string for a suggestion
 *   onSelect(item)
 * })
 */
export default function autocomplete(input, {url, display, render, onSelect, minLength = 1, delay = 150}) {
    const wrapper = document.createElement('div');
    wrapper.className = 'autocomplete';
    input.parentNode.insertBefore(wrapper, input);
    wrapper.appendChild(input);
    const menu = document.createElement('div');
    menu.className = 'autocomplete-menu list-group shadow hidden';
    wrapper.appendChild(menu);

    let timer = null;
    let counter = 0;

    const close = () => menu.classList.add('hidden');
    const escape = (s) => String(s).replace(/[&<>"']/g, c => ({'&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;'}[c]));

    function show(items) {
        menu.innerHTML = '';
        if (items.length === 0) {
            menu.innerHTML = '<div class="list-group-item">Nothing found.</div>';
        }
        items.forEach(item => {
            const a = document.createElement('a');
            a.href = '#';
            a.className = 'list-group-item list-group-item-action';
            a.innerHTML = render(item, escape);
            a.addEventListener('mousedown', e => e.preventDefault()); // keep focus in the input
            a.addEventListener('click', e => {
                e.preventDefault();
                input.value = display(item);
                close();
                onSelect(item);
            });
            menu.appendChild(a);
        });
        menu.classList.remove('hidden');
    }

    async function search() {
        const q = input.value.trim();
        if (q.length < minLength) {
            close();
            return;
        }
        const id = ++counter;
        const response = await fetch(`${url}?q=${encodeURIComponent(q)}`, {headers: {'Accept': 'application/json'}});
        if (!response.ok || id !== counter) {
            return; // failed or a newer query is already pending
        }
        show(await response.json());
    }

    input.addEventListener('input', () => {
        clearTimeout(timer);
        timer = setTimeout(search, delay);
    });
    input.addEventListener('blur', close);
    input.addEventListener('keydown', e => {
        if (e.key === 'Escape') {
            close();
        }
    });
}
