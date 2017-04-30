const {mix} = require('laravel-mix');

/*
 |--------------------------------------------------------------------------
 | Mix Asset Management
 |--------------------------------------------------------------------------
 |
 | Mix provides a clean, fluent API for defining some Webpack build steps
 | for your Laravel application. By default, we are compiling the Sass
 | file for the application as well as bundling up all the JS files.
 |
 */

mix.js('resources/assets/js/app.js', 'public/js')
    .extract([
        'lodash',
        'jquery',
        'bootstrap-sass',
        'datatables.net',
        'datatables.net-buttons',
        'datatables.net-buttons/js/buttons.html5',
        'datatables.net-bs',
        'bloodhound-js',
        'typeahead.js',
        'datatables.net-buttons-bs',
        'pdfmake-browserified',
    ])
    .version();

mix.sass('resources/assets/sass/app.scss', 'public/css')
    .version();
