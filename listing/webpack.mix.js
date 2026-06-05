const mix = require('laravel-mix')
const glob = require('glob')
const fs = require('fs')
const path = require('path')

const originalCopy = mix.copy.bind(mix)
const deferredCopies = []

mix.copy = function (from, to, ...args) {
    if (typeof from === 'string' && from.replace(/\\/g, '/').startsWith('public/')) {
        deferredCopies.push([from, to])

        return this
    }

    return originalCopy(from, to, ...args)
}

mix.after(() => {
    deferredCopies.forEach(([from, to]) => copyBuiltPath(from, to))
})

mix.options({
    processCssUrls: false,
    clearConsole: true,
    terser: {
        extractComments: false,
    },
    manifest: false,
})

mix.webpackConfig({
    stats: {
        children: false,
    },
    externals: {
        vue: 'Vue',
    },
})

mix.disableSuccessNotifications()

mix.vue()

let buildPaths = []

function pushToPath(path, type) {
    buildPaths.push(`${type}/${path === 'true' ? '*' : path}`)
}

const types = [
    {
        key: 'npm_config_theme',
        name: 'themes',
    },
    {
        key: 'npm_config_plugin',
        name: 'plugins',
    },
    {
        key: 'npm_config_package',
        name: 'packages',
    },
    {
        key: 'npm_config_core',
        name: 'core',
    },
]

for (const assetType of types) {
    const assetPath = process.env[assetType.key]

    if (! assetPath) {
        continue
    }

    pushToPath(assetPath, assetType.name)
}

if (! buildPaths.length) {
    buildPaths = ['*/*']
}

buildPaths.forEach(buildPath => glob.sync(`./platform/${buildPath}/webpack.mix.js`).forEach(item => require(__dirname + '/' + item)))

function copyBuiltPath(from, to) {
    const target = fs.existsSync(from) && fs.statSync(from).isFile() && !path.extname(to)
        ? path.join(to, path.basename(from))
        : to

    fs.mkdirSync(path.dirname(target), { recursive: true })
    fs.cpSync(from, target, { recursive: true })
}
