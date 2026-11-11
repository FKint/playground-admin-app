// Must be imported before the jQuery plugins: they attach themselves to window.jQuery when loaded.
import jQuery from 'jquery';
import JSZip from 'jszip';

window.$ = window.jQuery = jQuery;
window.JSZip = JSZip;
