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

pdfMake.addVirtualFileSystem(pdfFonts);

Object.assign(Datepicker.locales, nl);
const datepickerDefaults = {
    language: 'nl-BE',
    format: 'yyyy-mm-dd',
    autohide: true,
    todayButton: true,
    todayHighlight: true,
    weekStart: 1,
};

window.bootstrap = bootstrap;
window.Datepicker = class extends Datepicker {
    constructor(element, options = {}) {
        super(element, {...datepickerDefaults, ...options});
    }
};
window.autocomplete = autocomplete;
