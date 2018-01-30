window._ = require('lodash');
window.$ = window.jQuery = require('jquery');

require('bootstrap-less');

require('datatables.net');
require('datatables.net-buttons');
require('datatables.net-buttons/js/buttons.html5');
require('datatables.net-bs');
require('datatables.net-buttons-bs');
require('datatables.net-buttons/js/buttons.colVis');
require('datatables.net-buttons-bs/js/buttons.bootstrap');

$.extend(true, $.fn.dataTable.defaults, {
    "language": {"url": "/dataTables.Dutch.lang"}
});

window.pdfMake = {createPdf: require('pdfmake-browserified')};


window.Bloodhound = require('bloodhound-js');
require('corejs-typeahead');

require('bootstrap-datepicker');
require('bootstrap-datepicker/js/locales/bootstrap-datepicker.nl-BE.js');
$.fn.datepicker.defaults.language = "nl-BE";
$.fn.datepicker.defaults.format = "yyyy-mm-dd";
$.fn.datepicker.defaults.autoclose = true;
$.fn.datepicker.defaults.todayBtn = "linked";
$.fn.datepicker.defaults.todayHighlight = true;
$.fn.datepicker.defaults.weekStart = 1;

window.moment = require('moment');