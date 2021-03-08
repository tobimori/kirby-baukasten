// system tools
const fs = require('fs')
const path = require('path')

// build tools
const esbuild = require('esbuild')

const sass = require('sass')
const Fiber = require('fibers')
const postcss = require('postcss')

// config
const { buildEnv } = require('../../coralic-kirby.config')

// env helper
const env = process.env.NODE_ENV === 'DEVELOPMENT' ? 'dev' : 'prod'

const buildJs = async (files) => {
  await esbuild
    .build({
      entryPoints: files,
      outdir: path.join('dist', env, 'js'),
      bundle: true,
      minify: buildEnv[env].js.minify,
      sourcemap: buildEnv[env].js.sourcemap,
      target: buildEnv[env].js.target,
    })
    .catch(() => process.exit(1))
}

const buildScss = async (files) => {
  await files.map(async (file) => {
    await sass.render(
      {
        file: file,
        fiber: Fiber,
      },
      (err, result) => {
        if (!err) {
          postcss(buildEnv[env].scss.postCssPlugins)
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
        } else {
          console.error(err)
        }
      }
    )
  })
}
module.exports = { buildJs, buildScss }
