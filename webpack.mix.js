const {mix} = require('laravel-mix');
//
// mix.webpackConfig({
//     resolve: {
//         alias: {
//             'is-array': require.resolve('node_modules/node-libs-browser/node_modules/buffer/node_modules/is-array'),
//             'ieee754': require.resolve('node_modules/node-libs-browser/node_modules/buffer/node_modules/ieee754'),
//             'base64-js': require.resolve('node_modules/node-libs-browser/node_modules/buffer/node_modules/base64-js')
//         }
//     }
// })
mix.webpackConfig({
    //node: {
    //    fs: "empty"
    //}
});
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
        'datatables.net-bs',
        'bloodhound-js',
        'typeahead.js'
    ])
    .version();

mix.sass('resources/assets/sass/app.scss', 'public/css')
    .version();
