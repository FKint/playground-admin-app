const mix = require('laravel-mix');

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
mix.setPublicPath('public');
mix.webpackConfig({
    resolve: {
        alias: {
            jquery: "jquery/src/jquery"
        }
    }
});

mix.js('resources/assets/js/app.js', 'public/js')
    .extract()
    .version();

mix.less('resources/assets/less/app.less', 'public/css')
    .version();

mix.copy('node_modules/datatables.net-plugins/i18n/nl-NL.json', 'public/dataTables.Dutch.json');
