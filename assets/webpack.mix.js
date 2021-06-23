const mix = require('laravel-mix');
require('dotenv').config();

/*
 |--------------------------------------------------------------------------
 | Mix Asset Management
 |--------------------------------------------------------------------------
 |
 | Mix provides a clean, fluent API for defining some Webpack build steps
 | for your WordPlate applications. By default, we are compiling the CSS
 | file for the application as well as bundling up all the JavaScript files.
 |
 */

const domain = process.env.APP_URL;

mix.disableSuccessNotifications()
    .setResourceRoot('./')
    .setPublicPath('./')
    .js('src/js/main.js', 'script/main.js')
    .sass('src/scss/main.scss', 'css/main.css')
    .sourceMaps()
    .browserSync('http://' + domain);