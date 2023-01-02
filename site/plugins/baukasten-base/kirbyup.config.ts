import { resolve } from 'path'
import { defineConfig } from 'kirbyup/config'
import autoprefixer from 'autoprefixer'

export default defineConfig({
  alias: {
    '@web/': `${resolve(__dirname, '../../../src/')}/`
  },
  extendViteConfig: {
    css: {
      postcss: {
        plugins: [autoprefixer()]
      }
    }
  }
})
