import { resolve } from 'path'
import { defineConfig } from 'vite'
import autoprefixer from 'autoprefixer'
import 'dotenv/config'
import postcssLogical from 'postcss-logical'
import laravel from 'laravel-vite-plugin'

export default defineConfig(({ mode }) => ({
  build: {
    outDir: resolve(__dirname, 'public/dist'),
    emptyOutDir: true
  },
  plugins: [
    laravel({
      input: ['src/index.ts', 'src/styles/index.scss', 'src/styles/panel.scss'],
      refresh: ['storage/content/**/*', 'site/{layouts,snippets,templates}/**/*']
    })
  ],
  resolve: {
    alias: {
      '@styles': resolve(__dirname, 'src/styles/'),
      '@': resolve(__dirname, 'src/')
    }
  },
  css: {
    postcss: {
      plugins: [autoprefixer(), postcssLogical()]
    }
  }
}))
