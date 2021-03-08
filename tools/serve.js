// system libs
const fs = require('fs')
const path = require('path')
const os = require('os')
const process = require('process')
const glob = require('glob')

// server tools
const browserSync = require('browser-sync')
const phpServer = require('php-server-manager')

// build tools
const esbuild = require('esbuild')

const sass = require('sass')
const Fiber = require('fibers')

const postcss = require('postcss')
const autoprefixer = require('autoprefixer')
const cssnano = require('cssnano')

// aliases
const { log } = console

// config
const config = require('../coralic-kirby.config')

let host, port, phpInstance, bsInstance

const isMacOS = os.platform() === 'darwin'

const buildJs = async (files) =>
  await esbuild
    .build({
      entryPoints: files,
      outdir: 'dist/dev/js/',
      bundle: true,
      minify: true,
      sourcemap: true,
      target: ['es2020'],
    })
    .catch(() => process.exit(1))

const buildScss = async (files) =>
  await files.map(
    async (file) =>
      await sass.render(
        {
          file: file,
          fiber: Fiber,
        },
        function (err, result) {
          if (!err)
            postcss([autoprefixer, cssnano])
              .process(result.css, {
                from: file,
                to: 'dist/dev/css/style.css',
              })
              .then((postCssResult) => {
                fs.writeFile(
                  'dist/dev/css/style.css',
                  postCssResult.css,
                  (err) => err && console.error(err)
                )
              })
          else console.error(err)
        }
      )
  )

function startPhp() {
  if (!config.devServer.proxy) {
    log('Starting PHP Server...')

    host = config.devServer.phpHost
    port = config.devServer.port

    phpInstance = new phpServer({
      php: config.devServer.phpBinary || 'php',
      port: port || '9000',
      host: host || 'localhost',
      script: path.join(path.resolve(__dirname, '..'), 'kirby', 'router.php'),
      directory: '.',
    })

    phpInstance.run(() => {
      log('Success! Starting browser-sync Server...') // todo: failure detection
      startBs()
    })
  } else {
    log('Using local development environment...')

    host = config.devServer.proxy.split(':')[0]
    port = config.devServer.proxy.split(':')[1] || 80

    startBs()
  }
}

function startBs() {
  bsInstance = browserSync.create('PHP Proxy')

  if (isMacOS && host === 'localhost') {
    log(
      `\nOn macOS, browser-sync can't reach PHP's built-in server through localhost.\nProxying [::1]:${port} instead.\n`
    )
    host = '[::1]'
  }

  const browserSyncConfig = {
    proxy: {
      target: `${host}:${port}`,
      proxyReq: [
        (proxyReq, req, res) => {
          proxyReq.setHeader('X-Forwarded-For', 'coralic-kirby')
          proxyReq.setHeader('X-Forwarded-Host', req.headers.host)
          proxyReq.setHeader('X-Forwarded-Proto', 'http')
        },
      ],
    },
    port: config.devServer.bsPort,
    ui: {
      port: config.devServer.bsUi,
    },
    logPrefix: '',
    open: false,
    reloadOnRestart: true,
    notify: false,
  }

  // clean up
  fs.rmdirSync('dist/dev/', { recursive: true }) // delete dev folder
  fs.mkdirSync('dist/dev/') // recreate them empty
  fs.mkdirSync('dist/dev/js/')
  fs.mkdirSync('dist/dev/css/')

  config.entryPoints.js.map((entry) => {
    bsInstance.watch(entry, (e, file) => {
      // create watcher for each entry point set in config
      if (e === 'change') {
        log(`Detected change to file ${file}, building to 'dist/dev/js/'...`)
        buildJs([file])
        log('Done!')
        bsInstance.reload('*.js')
      }
    })
    glob(entry, (err, files) => {
      // glob match files and create clean build on start
      if (!err)
        buildJs(files) && log(`Building bundles from:\n${files.join('\n')}\n`)
      else console.error(err)
    })
  })

  config.entryPoints.scss.map((entry) => {
    bsInstance.watch(entry, (e, file) => {
      // create watcher for each entry point set in config
      if (e === 'change') {
        log(`Detected change to file ${file}, building to 'dist/dev/css/'...`)
        buildScss([file])
        log('Done!')
        bsInstance.reload('*.css')
      }
    })
    glob(entry, (err, files) => {
      // glob match files and create clean build on start
      if (!err) buildScss(files)
      else console.error(err)
    })
  })

  bsInstance.init(browserSyncConfig)
}

startPhp()
