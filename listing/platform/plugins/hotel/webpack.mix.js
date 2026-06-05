let mix = require('laravel-mix')
const fs = require('fs')

const path = require('path')
let directory = path.basename(path.resolve(__dirname))

const source = 'platform/plugins/' + directory
const dist = 'public/vendor/core/plugins/' + directory

mix

    .js(source + '/resources/assets/js/currencies.js', dist + '/js')

    .js(source + '/resources/assets/js/customer.js', dist + '/js')

    .js(source + '/resources/assets/js/avatar.js', dist + '/js')

    .js(source + '/resources/assets/js/utilities.js', dist + '/js')

    .js(source + '/resources/assets/js/room-availability.js', dist + '/js')

    .js(source + '/resources/assets/js/booking-reports.js', dist + '/js')

    .js(source + '/resources/assets/js/coupon.js', dist + '/js')

const styles = [
    'hotel.scss',
    'currencies.scss',
    'customer.scss',
    'review.scss',
];

styles.forEach(item => {
    mix.sass(source + '/resources/assets/sass/' + item, dist + '/css');
});

if (mix.inProduction()) {
    mix.after(() => {
        [
            'currencies.js',
            'customer.js',
            'avatar.js',
            'utilities.js',
            'room-availability.js',
            'booking-reports.js',
            'coupon.js',
        ].forEach(item => {
            copyBuiltAsset(dist + '/js/' + item, source + '/public/js/' + item);
        });

        styles.forEach(item => {
            const cssFile = item.replace('.scss', '.css');

            copyBuiltAsset(dist + '/css/' + cssFile, source + '/public/css/' + cssFile);
        });
    });
}

function copyBuiltAsset(from, to) {
    fs.mkdirSync(path.dirname(to), { recursive: true });
    fs.copyFileSync(from, to);
}
