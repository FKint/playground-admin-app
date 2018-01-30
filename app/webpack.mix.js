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
mix.webpackConfig({
    resolve: {
        alias: {
            jquery: "jquery/src/jquery"
        }
    }
});

mix.js('resources/assets/js/app.js', 'public/js')
    .extract([
        'lodash',
        'jquery',
        'bootstrap-less',
        'datatables.net',
        'datatables.net-buttons',
        'datatables.net-buttons/js/buttons.html5',
        'datatables.net-bs',
        'datatables.net-buttons-bs',
        'datatables.net-buttons/js/buttons.colVis',
        'datatables.net-buttons-bs/js/buttons.bootstrap',
        'bloodhound-js',
        'corejs-typeahead',
        'pdfmake-browserified',
        'bootstrap-datepicker',
        'bootstrap-datepicker/js/locales/bootstrap-datepicker.nl-BE.js',
        'moment'
    ])
    .version();

mix.less('resources/assets/less/app.less', 'public/css')
    .version();