// Must be imported before the jQuery plugins: they attach themselves to window.jQuery when loaded.
import _ from 'lodash';
import jQuery from 'jquery';
import JSZip from 'jszip';

window._ = _;
window.$ = window.jQuery = jQuery;
window.JSZip = JSZip;
