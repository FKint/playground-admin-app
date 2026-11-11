import './globals';

import 'bootstrap-less';

import 'datatables.net';
import 'datatables.net-buttons';
import 'datatables.net-buttons/js/buttons.html5';
import 'datatables.net-bs';
import 'datatables.net-buttons-bs';
import 'datatables.net-buttons/js/buttons.colVis';
import 'datatables.net-buttons-bs/js/buttons.bootstrap';
import dutch from 'datatables.net-plugins/i18n/nl-NL.json';

import pdfMake from 'pdfmake/build/pdfmake.js';
import pdfFonts from 'pdfmake/build/vfs_fonts.js';

import Bloodhound from 'bloodhound-js';
import 'corejs-typeahead';

import 'bootstrap-datepicker';
import 'bootstrap-datepicker/js/locales/bootstrap-datepicker.nl-BE.js';

import moment from 'moment/moment';

$.extend(true, $.fn.dataTable.defaults, {
    "language": dutch,
});

pdfMake.addVirtualFileSystem(pdfFonts);

window.Bloodhound = Bloodhound;

$.fn.datepicker.defaults.language = "nl-BE";
$.fn.datepicker.defaults.format = "yyyy-mm-dd";
$.fn.datepicker.defaults.autoclose = true;
$.fn.datepicker.defaults.todayBtn = "linked";
$.fn.datepicker.defaults.todayHighlight = true;
$.fn.datepicker.defaults.weekStart = 1;

window.moment = moment;
