import { resolve } from 'path'
import { mkdirSync, writeFileSync } from 'fs'
import { defineConfig } from 'vite'
import FullReload from 'vite-plugin-full-reload'
import type { Plugin as PostCssPlugin } from 'postcss'

/**
 * Prevent FOUC in development mode before Vite
 * injects the CSS into the page
 */
const postCssViteDevCss = (): PostCssPlugin => ({
  postcssPlugin: 'postcss-vite-dev-css',

  OnceExit(root, { result }) {
    // @ts-expect-error: property unknown
    if (result.opts.env !== 'production') {
      const outDir = resolve(__dirname, 'public/dist/dev')
      mkdirSync(outDir, { recursive: true })
      writeFileSync(resolve(outDir, 'index.css'), root.toResult().css)
    }
  }
})

export default defineConfig(({ mode }) => ({
  root: 'src',
  base: mode === 'development' ? '/dist/' : '/dist/',

  build: {
    outDir: resolve(__dirname, 'public/dist'),
    emptyOutDir: true,
    manifest: true,
    rollupOptions: {
      input: resolve(__dirname, 'src/index.ts')
    }
  },

  css: {
    postcss: {
      plugins: [postCssViteDevCss()]
    }
  },

  plugins: [FullReload(['storage/content/**/*', 'site/{layouts,snippets,templates}/**/*'])],

  server: {
    cors: true,
    port: 3000,
    strictPort: true
  }
}))
