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
        'bloodhound-js',
        'typeahead.js',
        'corejs-typeahead',
        'datatables.net-buttons-bs',
        'pdfmake-browserified',
    ])
    .version();

mix.less('resources/assets/less/app.less', 'public/css')
    .version();