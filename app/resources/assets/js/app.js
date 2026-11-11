import './globals';

import * as bootstrap from 'bootstrap';
import '../sass/app.scss';

import 'datatables.net-bs5';
import 'datatables.net-buttons-bs5';
import 'datatables.net-buttons/js/buttons.html5';
import 'datatables.net-buttons/js/buttons.colVis';
import dutch from 'datatables.net-plugins/i18n/nl-NL.json';

import pdfMake from 'pdfmake/build/pdfmake.js';
import pdfFonts from 'pdfmake/build/vfs_fonts.js';

import {Datepicker} from 'vanillajs-datepicker';
import nl from 'vanillajs-datepicker/locales/nl-BE';

import autocomplete from './autocomplete';

$.extend(true, $.fn.dataTable.defaults, {
    "language": dutch,
});

$.fn.dataTable.Buttons.defaults.dom.button.className = 'btn btn-outline-secondary';

pdfMake.addVirtualFileSystem(pdfFonts);

Object.assign(Datepicker.locales, nl);
// TODO: the registrations and transactions date pickers used to show 'yyyy/mm/dd' (data-date-format); all pickers now share the
// ISO format below. Confirm that this is wanted, or pass {format: 'yyyy/mm/dd'} to those two pickers.
const datepickerDefaults = {
    language: 'nl-BE',
    format: 'yyyy-mm-dd',
    autohide: true,
    todayButton: true,
    todayHighlight: true,
    weekStart: 1,
    buttonClass: 'btn',
};

window.bootstrap = bootstrap;
window.Datepicker = class extends Datepicker {
    constructor(element, options = {}) {
        super(element, {...datepickerDefaults, ...options});
        // Close the popup as soon as a complete date has been typed, so it doesn't cover the form.
        element.addEventListener('input', () => {
            if (/^\d{4}-\d{2}-\d{2}$/.test(element.value)) {
                this.hide();
            }
        });
    }
};
window.autocomplete = autocomplete;
