import { resolve } from 'path'
import { defineConfig } from 'vite'
import laravel from 'laravel-vite-plugin'
import 'dotenv/config'

export default defineConfig(({ mode }) => ({
  base: mode === 'development' ? '/' : '/dist/',

  build: {
    outDir: resolve(__dirname, 'public/dist'),
    emptyOutDir: true,
    manifest: 'manifest.json'
  },
  plugins: [
    laravel({
      input: ['src/index.ts', 'src/styles/index.css', 'src/styles/panel.css'],
      refresh: ['site/{layouts,snippets,templates}/**/*'],
      transformOnServe: (code) =>
        code.replaceAll(
          `/assets`,
          `http://${process.env.KIRBY_DEV_HOSTNAME ?? '127.0.0.1'}:${
            process.env.KIRBY_DEV_PORT ?? '3000'
          }/assets`
        )
    })
  ],
  resolve: {
    alias: {
      '@styles': resolve(__dirname, 'src/styles/'),
      '@': resolve(__dirname, 'src/')
    }
  }
}))
