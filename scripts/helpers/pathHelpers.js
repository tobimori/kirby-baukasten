const path = require('path')

const { env } = require('./generalHelpers')

const getDistPath = (sourcePath) =>
  path.join(
    'dist',
    env,
    'css',
    path.basename(sourcePath).replace('.scss', '.css')
  )

module.exports = { getDistPath }
