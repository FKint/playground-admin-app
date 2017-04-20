window._ = require('lodash');
window.$ = window.jQuery = require('jquery');

require('bootstrap-sass');

require('datatables.net');
require('datatables.net-bs');
$.extend(true, $.fn.dataTable.defaults, {
    "language": {"url": "//cdn.datatables.net/plug-ins/1.10.13/i18n/Dutch.json"}
});

window.Bloodhound = require('bloodhound-js');
require('typeahead.js');