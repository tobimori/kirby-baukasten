/***
 *                      _ _      _   _     _                _      _      _   _ _
 *      __ ___ _ _ __ _| (_)__  | |_(_)_ _| |__ _  _   _ __| |__ _(_)_ _ | |_(_) |_
 *     / _/ _ \ '_/ _` | | / _| | / / | '_| '_ \ || | | '_ \ / _` | | ' \| / / |  _|
 *     \__\___/_| \__,_|_|_\__| |_\_\_|_| |_.__/\_, | | .__/_\__,_|_|_||_|_\_\_|\__|
 *                                              |__/  |_|
 *
 *     CORALIC KIRBY PLAINKIT // CONFIGURATION FILE
 */

const entryPoints = {
  js: ['src/js/*.js'],
  scss: ['src/scss/*.scss'],
}

const devServer = {
  // Port used by PHP dev server
  port: 9000, // will be proxied by browser-sync

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
}

module.exports = { devServer, entryPoints }
