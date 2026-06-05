const mix = require('laravel-mix')
const path = require('path')
const fs = require('fs')

const directory = path.basename(path.resolve(__dirname))
const source = `platform/packages/${directory}`
const dist = `public/vendor/core/packages/${directory}`

mix
    .js(`${source}/resources/js/theme-options.js`, `${dist}/js`)
    .js(`${source}/resources/js/theme.js`, `${dist}/js`)
    .js(source + '/resources/js/icons-field.js', dist + '/js')
    .js(source + '/resources/js/toast.js', dist + '/js')
    .sass(`${source}/resources/sass/theme-options.scss`, `${dist}/css`)
    .sass(`${source}/resources/sass/admin-bar.scss`, `${dist}/css`)
    .sass(`${source}/resources/sass/guideline.scss`, `${dist}/css`)

if (mix.inProduction()) {
    mix.after(() => {
        copyBuiltAsset(`${dist}/js/theme-options.js`, `${source}/public/js/theme-options.js`)
        copyBuiltAsset(`${dist}/js/theme.js`, `${source}/public/js/theme.js`)
        copyBuiltAsset(dist + '/js/icons-field.js', source + '/public/js/icons-field.js')
        copyBuiltAsset(dist + '/js/toast.js', source + '/public/js/toast.js')
        copyBuiltAsset(`${dist}/css/theme-options.css`, `${source}/public/css/theme-options.css`)
        copyBuiltAsset(`${dist}/css/admin-bar.css`, `${source}/public/css/admin-bar.css`)
        copyBuiltAsset(`${dist}/css/guideline.css`, `${source}/public/css/guideline.css`)
    })
}

function copyBuiltAsset(from, to) {
    fs.mkdirSync(path.dirname(to), { recursive: true })
    fs.copyFileSync(from, to)
}
