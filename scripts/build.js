const glob = require('glob')
const { buildJs, buildScss } = require('./helpers/buildHelpers')
const { entryPoints } = require('../coralic-kirby.config')

entryPoints.js.map((entry) => {
  glob(entry, (err, files) => {
    if (!err) buildJs(files)
    else console.error(err)
  })
})

entryPoints.scss.map((entry) => {
  glob(entry, (err, files) => {
    if (!err) buildScss(files)
    else console.error(err)
  })
})
