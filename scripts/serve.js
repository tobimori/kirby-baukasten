// system libs
const fs = require('fs')
const path = require('path')
const process = require('process')
const glob = require('glob')
const spawn = require('child_process').spawn

// server tools
const browserSync = require('browser-sync')

// aliases
const { log } = console

// config
const {
  buildEnv,
  entryPoints,
  devServer,
  watchPoints,
} = require('../coralic-kirby.config')

// helpers
const { buildJs, buildScss } = require('./helpers/buildHelpers')
const { env, isMacOS } = require('./helpers/generalHelpers')

let host, port, phpInstance, bsInstance
process.env.ENV = process.env.NODE_ENV

function startPhp() {
  if (!devServer.proxy) {
    log('Starting PHP Server...')

    host = devServer.phpHost
    port = devServer.port

    const params = [
      '-S',
      `${host || 'localhost'}:${port || '9000'}`,
      '-t',
      '.',
      path.join(path.resolve(__dirname, '..'), 'kirby', 'router.php'),
    ]

    const phpProcess = spawn(devServer.phpBinary || 'php', params, {
      stdio: 'inherit',
      env: process.env,
    })

    phpProcess.on('close', () => console.log('PHP Server closed'))
    phpProcess.on('error', (error) => console.error('PHP Server error', error))

    log('Success! Starting browser-sync Server...') // todo: failure detection
    startBs()
  } else {
    log('Using local development environment...')

    host = devServer.proxy.split(':')[0]
    port = devServer.proxy.split(':')[1] || 80

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
    port: devServer.bsPort,
    ui: {
      port: devServer.bsUi,
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

  watchPoints.js.map((entry) => {
    bsInstance.watch(entry, (e, file) => {
      // create watcher for each entry point set in config
      if (e === 'change') {
        log(`Detected change to file ${file}, building to 'dist/dev/js/'...`)
        glob(entry, (err, files) => {
          if (!err)
            buildJs(files) &&
              log(`Building bundles from:\n${files.join('\n')}\n`)
          else console.error(err)
        })
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

  watchPoints.scss.map((entry) => {
    bsInstance.watch(entry, (e, file) => {
      // create watcher for each entry point set in config
      if (e === 'change') {
        log(`Detected change to file ${file}, building to 'dist/dev/css/'...`)
        glob(entry, (err, files) => {
          if (!err) buildScss(files)
          else console.error(err)
        })
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
