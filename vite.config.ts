import { resolve } from "node:path"
import inject from "@rollup/plugin-inject"
import tailwind from "@tailwindcss/vite"
import browserslist from "browserslist"
import laravel from "laravel-vite-plugin"
import { browserslistToTargets } from "lightningcss"
import { defineConfig, loadEnv } from "vite"
import devtoolsJson from "vite-plugin-devtools-json"
import svgSprite from "vite-svg-sprite-wrapper"
import tsconfigPaths from "vite-tsconfig-paths"
import { browserslist as browserslistConfig } from "./package.json"

export default defineConfig(({ mode }) => {
	const env = loadEnv(mode, process.cwd(), "")

	return {
		base: mode === "development" ? "/" : "/dist/",

		build: {
			outDir: resolve(__dirname, "public/dist"),
			emptyOutDir: true,
			manifest: "manifest.json",
			cssMinify: "lightningcss"
		},
		plugins: [
			// this plugin is necessary for our HTMX extensions to correctly register
			inject({
				htmx: "htmx.org"
			}),
			svgSprite({
				sprite: {
					shape: {
						transform: [
							{
								svgo: {
									plugins: [{ name: "preset-default" }, "removeXMLNS"]
								}
							}
						]
					}
				},
				icons: "assets/icons/*.svg",
				outputDir: "assets/"
			}),
			laravel({
				input: ["src/index.ts", "src/styles/index.css", "src/styles/panel.css"],
				refresh: ["site/{layouts,snippets,templates}/**/*"]
			}),
			tsconfigPaths(),
			tailwind(),
			devtoolsJson()
		],
		css: {
			transformer: "lightningcss",
			lightningcss: {
				targets: browserslistToTargets(browserslist(browserslistConfig))
			}
		},
		server: {
			origin: env.APP_URL,
			port: Number(env.VITE_DEV_PORT || 3000),
			proxy: {
				// we proxy anything except the folders our vite dev assets are in
				"^(?!/src|/node_modules|/@vite|/@react-refresh|/assets).*$": `http://${env.KIRBY_DEV_HOSTNAME}:${env.KIRBY_DEV_PORT}`
			}
		}
	}
})
