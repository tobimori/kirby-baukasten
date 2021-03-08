module.exports = {
  entryPoints: {
    js: ['src/js/*.js'],
    scss: ['src/scss/*.scss'],
  },

  buildEnv: {
    dev: {
      scss: {
        postCssPlugins: [require('autoprefixer')],
      },
      js: {
        minify: true,
        sourcemap: true,
        target: ['es2020'],
      },
    },
    prod: {
      scss: {
        postCssPlugins: [require('autoprefixer'), require('cssnano')],
      },
      js: {
        minify: true,
        sourcemap: false,
        target: ['es2020'],
      },
    },
  },

  devServer: {
    // Port used by PHP dev server
    port: 9001, // will be proxied by browser-sync

    // Ports used by browser-sync
    bsPort: 3000, // site port
    bsUi: 3001, // port for configuration ui

    // Set this option to the hostname of your local environment
    // if you want to use such one (like Laravel Valet)
    proxy: '',

    // The alias/path to the php binary, use proxy option for custom server.
    phpBinary: 'php',

    // Host used by the php built-in server. Only used when proxy is false.
    // Default is localhost or [::1] (IPv6 localhost) on macOS.
    phpHost: 'localhost',

    // You might need to change config/config.localhost.php into config.[YOURHOST].php
    // when using a different phpHost or not using the included PHP dev server
  },
}
