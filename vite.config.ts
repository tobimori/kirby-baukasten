import { exec } from "node:child_process"
import { resolve } from "node:path"
import { promisify } from "node:util"
import inject from "@rollup/plugin-inject"
import tailwind from "@tailwindcss/vite"
import browserslist from "browserslist"
import laravel from "laravel-vite-plugin"
import { browserslistToTargets } from "lightningcss"
import { defineConfig, loadEnv, type Plugin } from "vite"
import devtoolsJson from "vite-plugin-devtools-json"
import svgSprite from "vite-svg-sprite-wrapper"
import tsconfigPaths from "vite-tsconfig-paths"
import { browserslist as browserslistConfig } from "./package.json"

const execAsync = promisify(exec)

const kirbyTypes = (): Plugin => {
	let isGeneratingTypes = false

	return {
		name: "kirby-types",
		configureServer(server) {
			server.watcher.add(resolve(__dirname, "site/blueprints/**/*.yml"))

			server.watcher.on("change", async (file) => {
				if (file.endsWith(".yml") && file.includes("site/blueprints") && !isGeneratingTypes) {
					isGeneratingTypes = true

					try {
						await execAsync("kirby types:create --force")
					} catch (error) {
						console.error("❌ Type generation failed:", error)
					} finally {
						isGeneratingTypes = false
					}
				}
			})
		}
	}
}

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
			devtoolsJson(),
			kirbyTypes()
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
