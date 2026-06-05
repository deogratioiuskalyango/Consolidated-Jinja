let mix = require('laravel-mix');
const purgeCssPackage = require('@fullhuman/postcss-purgecss');
const purgeCss = purgeCssPackage.default || purgeCssPackage;
const fs = require('fs');

const path = require('path');
let directory = path.basename(path.resolve(__dirname));

const source = 'platform/themes/' + directory;
const dist = 'public/themes/' + directory;

mix
    .sass(
        source + '/assets/sass/style.scss',
        dist + '/css',
        {},
        [
            purgeCss({
                content: [
                    source + '/assets/js/components/*.vue',
                    source + '/layouts/*.blade.php',
                    source + '/partials/*.blade.php',
                    source + '/partials/**/*.blade.php',
                    source + '/views/*.blade.php',
                    source + '/views/**/*.blade.php',
                    source + '/views/**/**/*.blade.php',
                    source + '/views/**/**/**/*.blade.php',
                    source + '/views/**/**/**/*.blade.php',
                    source + '/widgets/**/templates/frontend.blade.php',
                ],
                defaultExtractor: content => content.match(/[\w-/.:]+(?<!:)/g) || [],
                safelist: [
                    /^navigation-/,
                    /^label-/,
                    /^status-/,
                    /^owl-/,
                    /^fa-/,
                    /^language/,
                    /^pagination/,
                    /^page-/,
                    /show-admin-bar/,
                    /breadcrumb/,
                    /active/,
                    /header-sticky/,
                    /show/,
                    /auth-/,
                ],
            })
        ]
    )

    .sass(source + '/assets/sass/rtl-style.scss', dist + '/css')

    .js(source + '/assets/js/app.js', dist + '/js')
    .js(source + '/assets/js/wishlist.js', dist + '/js')
    .js(source + '/assets/js/property.js', dist + '/js')
    .js(source + '/assets/js/review.js', dist + '/js')

if (mix.inProduction()) {
    mix.after(() => {
        copyBuiltAsset(dist + '/css/style.css', source + '/public/css/style.css');
        copyBuiltAsset(dist + '/css/rtl-style.css', source + '/public/css/rtl-style.css');
        copyBuiltAsset(dist + '/js/app.js', source + '/public/js/app.js');
        copyBuiltAsset(dist + '/js/wishlist.js', source + '/public/js/wishlist.js');
        copyBuiltAsset(dist + '/js/property.js', source + '/public/js/property.js');
        copyBuiltAsset(dist + '/js/review.js', source + '/public/js/review.js');
    });
}

function copyBuiltAsset(from, to) {
    fs.mkdirSync(path.dirname(to), { recursive: true });
    fs.copyFileSync(from, to);
}
