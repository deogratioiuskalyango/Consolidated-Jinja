let mix = require('laravel-mix')
const fs = require('fs')

const path = require('path')
let directory = path.basename(path.resolve(__dirname))

const source = 'platform/plugins/' + directory
const dist = 'public/vendor/core/plugins/' + directory

mix
    .sass(source + '/resources/sass/faq.scss', dist + '/css')
    .js(source + '/resources/js/faq.js', dist + '/js')

if (mix.inProduction()) {
    mix.after(() => {
        copyBuiltAsset(dist + '/css/faq.css', source + '/public/css/faq.css')
        copyBuiltAsset(dist + '/js/faq.js', source + '/public/js/faq.js')
    })
}

function copyBuiltAsset(from, to) {
    fs.mkdirSync(path.dirname(to), { recursive: true })
    fs.copyFileSync(from, to)
}
