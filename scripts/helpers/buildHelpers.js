// system tools
const fs = require('fs')
const path = require('path')

// build tools
const esbuild = require('esbuild')

const sass = require('sass')
const postcss = require('postcss')

// config
const { buildEnv } = require('../../coralic-kirby.config')

// helpers
const { getDistPath } = require('./pathHelpers')
const { env } = require('./generalHelpers')

const buildJs = async (files) => {
  await esbuild
    .build({
      entryPoints: files,
      outdir: path.join('dist', env, 'js'),
      bundle: true,
      minify: buildEnv[env].js.minify,
      sourcemap: buildEnv[env].js.sourcemap,
      target: buildEnv[env].js.target
    })
    .catch((error) => console.error(error))
}

const buildScss = async (files) => {
  await files.map(async (file) => {
    try {
      const result = sass.compile(file)
      const distPath = getDistPath(file)
      postcss(buildEnv[env].scss.postCssPlugins)
        .process(result.css, {
          from: file,
          to: distPath
        })
        .then((postCssResult) => {
          fs.writeFile(
            distPath,
            postCssResult.css,
            (error) => error && console.error(error)
          )
        })
    } catch (error) {
      console.log(error)
    }
  })
}

module.exports = { buildJs, buildScss }
