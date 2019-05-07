const mix = require("laravel-mix");

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

const scssDir = "resources/assets/sass";
const jsDir = "resources/assets/js";
const cssPubDir = "public/css";
const jsPubDir = "public/js";

mix.webpackConfig(webpack => {
    return {
        plugins: [
            new webpack.ProvidePlugin({
                $: 'jquery',
                jQuery: 'jquery',
            })
        ],
    };
});

// Global CSS styles definition
mix.sass(`${scssDir}/app.scss`, cssPubDir)
    .sass(`${scssDir}/flowchart.scss`, cssPubDir)
    .sass(`${scssDir}/dashboard.scss`, cssPubDir)
    .sass(`${scssDir}/schedule.scss`, cssPubDir);

// Global Javascript Stuff
mix.js(`${jsDir}/app.js`, jsPubDir)
    .js(`${jsDir}/pages/scheduler.js`, jsPubDir)
    .extract();

mix.copy("node_modules/ion-sound/sounds/door_bell*", "public/sounds");

if (mix.inProduction()) {
    mix.version();
} else {
    mix.sourceMaps();
}
