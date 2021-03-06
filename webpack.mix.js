const mix = require('laravel-mix');

/*
 |--------------------------------------------------------------------------
 | Mix Asset Management
 |--------------------------------------------------------------------------
 |
 | Mix provides a clean, fluent API for defining some Webpack build steps
 | for your Laravel applications. By default, we are compiling the CSS
 | file for the application as well as bundling up all the JS files.
 |
 */
mix.disableNotifications();
mix.postCss('resources/css/app.css', 'public/css')
    .js('resources/js/show-comments.js', 'public/js')
    .js('resources/js/datatables/product-analysis.js', 'public/js')
    .js('resources/js/datatables/shopee-analysis.js', 'public/js')
    .js('resources/js/datatables/shop-offline.js', 'public/js')
    .js('resources/js/datatables/shopee-cate.js', 'public/js')
    .js('resources/js/show-reviews.js', 'public/js')
    .js('resources/js/charts/product-chart.js', 'public/js')
    .js('resources/js/charts/shop-chart.js', 'public/js')
    .js('resources/js/charts/category-chart.js', 'public/js')
    .js('resources/js/charts/market-share-chart.js', 'public/js');
