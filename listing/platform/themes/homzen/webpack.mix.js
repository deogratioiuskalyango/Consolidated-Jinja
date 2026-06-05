const mix = require('laravel-mix')
const path = require('path')
const fs = require('fs')

const directory = path.basename(path.resolve(__dirname))
const source = `platform/themes/${directory}`
const dist = `public/themes/${directory}`

mix
    .sass(`${source}/assets/sass/style.scss`, `${dist}/css`)
    .js(`${source}/assets/js/script.js`, `${dist}/js`)

if (mix.inProduction()) {
    mix.after(() => {
        copyBuiltAsset(`${dist}/css/style.css`, `${source}/public/css/style.css`)
        copyBuiltAsset(`${dist}/js/script.js`, `${source}/public/js/script.js`)
    })
}

function copyBuiltAsset(from, to) {
    fs.mkdirSync(path.dirname(to), { recursive: true })
    fs.copyFileSync(from, to)
}
