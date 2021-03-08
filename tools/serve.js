const path = require('path')
const os = require('os')
const browserSync = require('browser-sync')
const phpServer = require('php-server-manager')

const log = console.log

let phpInstance, bsInstance

function startPhp() {
  log('Starting PHP Server...')

  phpInstance = new phpServer({
    port: 9000,
    script: path.join(__dirname, 'kirby', 'router.php'),
    directory: '.',
  })

  phpInstance.run(() => {
    log('Success! Starting browser-sync Server...')
    startBs()
  })
}

function startBs() {
  bsInstance = browserSync.create()

  const browserSyncConfig = {
    files: ['src/**/*', 'site/**/*'],
    watchEvents: ['change'],
    watch: true,
    watchOptions: {
      ignoreInitial: true,
      ignored: [
        path.join('site', 'cache', '**/*'),
        path.join('site', 'accounts', '**/*'),
      ],
    },
    proxy: {
      target: '127.0.0.1:9000',
      proxyReq: [
        (proxyReq, req, res) => {
          proxyReq.setHeader('X-Forwarded-For', 'coralic-kirby')
          proxyReq.setHeader('X-Forwarded-Host', req.headers.host)
          proxyReq.setHeader('X-Forwarded-Proto', 'http')
        },
      ],
    },
    port: 3000,
    logPrefix: '',
    open: false,
    reloadOnRestart: true,
    notify: false,
  }

  bsInstance.init(browserSyncConfig)
}

startPhp()
