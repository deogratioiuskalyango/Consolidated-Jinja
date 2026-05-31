const mix = require('laravel-mix')
const path = require('path')

const directory = path.basename(path.resolve(__dirname))
const source = `platform/plugins/${directory}`
const dist = `public/vendor/core/plugins/${directory}`

const scripts = [
    'topup-management.js',
    'wallet.js',
    'withdrawal-management.js',
]

scripts.forEach((item) => {
    mix.js(`${source}/resources/js/${item}`, `${dist}/js`)
})

if (mix.inProduction()) {
    scripts.forEach((item) => {
        mix.copy(`${dist}/js/${item}`, `${source}/public/js`)
    })
}

const styles = [
    'wallet-admin.scss',
    'wallet-admin-rtl.scss',
    'wallet.scss',
]

styles.forEach((item) => {
    mix.sass(`${source}/resources/sass/${item}`, `${dist}/css`)
})

if (mix.inProduction()) {
    styles.forEach((item) => {
        mix.copy(`${dist}/css/${item.replace('.scss', '.css')}`, `${source}/public/css`)
    })
}
