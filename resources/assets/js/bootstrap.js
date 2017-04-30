window._ = require('lodash');
window.$ = window.jQuery = require('jquery');

require('bootstrap-sass');

require('datatables.net');
require('datatables.net-buttons');
require('datatables.net-buttons/js/buttons.html5');
require('datatables.net-bs');
require('datatables.net-buttons-bs');

$.extend(true, $.fn.dataTable.defaults, {
    "language": {"url": "//cdn.datatables.net/plug-ins/1.10.13/i18n/Dutch.json"}
});

window.pdfMake = {createPdf: require('pdfmake-browserified')};


window.Bloodhound = require('bloodhound-js');
require('typeahead.js');

require('bootstrap-datepicker');

window.moment = require('moment');