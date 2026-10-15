import { exec, spawn } from "node:child_process"
import { createHash } from "node:crypto"
import { once } from "node:events"
import { writeFileSync } from "node:fs"
import { createServer, type AddressInfo } from "node:net"
import { resolve } from "node:path"
import { promisify } from "node:util"
import { constants } from "node:zlib"

import svgSpritemap from "@spiriit/vite-plugin-svg-spritemap"
import tailwind from "@tailwindcss/vite"
import browserslist from "browserslist"
import laravel from "laravel-vite-plugin"
import { browserslistToTargets } from "lightningcss"
import { defineConfig, loadEnv, type Plugin } from "vite"
import { compression, defineAlgorithm } from "vite-plugin-compression2"
import devtoolsJson from "vite-plugin-devtools-json"

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

const publicUrls = [process.env.PORTLESS_URL, process.env.PORTLESS_TAILSCALE_URL].filter((url) => url !== undefined)

const kirbyServer = (port: number): Plugin => ({
	name: "kirby-server",
	apply: "serve",
	enforce: "post",
	configureServer(server) {
		const httpServer = server.httpServer
		if (!httpServer) return

		httpServer.once("listening", () => {
			writeFileSync(resolve(__dirname, "public/hot"), "")
			const php = spawn("php", ["-S", `127.0.0.1:${port}`, "-t", "public", "vendor/getkirby/cms/router.php"], {
				cwd: __dirname,
				stdio: ["ignore", "inherit", "pipe"],
				env: {
					...process.env,
					PHP_CLI_SERVER_WORKERS: "4",
					KIRBY_DEV_SERVER: "1",
					KIRBY_SESSION_COOKIE: `kirby_session_${createHash("md5").update(__dirname).digest("hex").slice(0, 8)}`
				}
			})

			php.stderr.on("data", (chunk: Buffer) => {
				const lines = chunk
					.toString()
					.split("\n")
					.filter((line) => line.trim() && !/ (Accepted|Closing)$| \[\d{3}\]: [A-Z]+ /.test(line))
				if (lines.length) process.stderr.write(lines.join("\n") + "\n")
			})

			const stop = () => php.kill()
			process.once("exit", stop)
			httpServer.once("close", () => {
				process.off("exit", stop)
				stop()
			})
		})
	}
})

export default defineConfig(async ({ mode }) => {
	const env = loadEnv(mode, process.cwd(), "")
	const probe = createServer().listen(0, "127.0.0.1")
	await once(probe, "listening")
	const kirbyPort = (probe.address() as AddressInfo).port
	probe.close()

	return {
		base: mode === "development" ? "/" : "/dist/",
		resolve: {
			alias: {
				"@": resolve(__dirname, "src")
			}
		},
		build: {
			outDir: resolve(__dirname, "public/dist"),
			emptyOutDir: true,
			manifest: "manifest.json",
			cssMinify: "lightningcss",
			rolldownOptions: {
				transform: {
					inject: {
						htmx: "htmx.org"
					}
				}
			}
		},
		optimizeDeps: {
			rolldownOptions: {
				transform: {
					inject: {
						htmx: "htmx.org"
					}
				}
			}
		},
		plugins: [
			svgSpritemap("assets/icons/*.svg", {
				prefix: "",
				svgo: { plugins: [{ name: "preset-default" }, "removeXMLNS"] },
				output: { use: false, view: false, hrefAttribute: "href" }
			}),
			laravel({
				input: ["src/index.ts", "src/styles/index.css", "src/styles/panel.css"],
				refresh: ["site/{layouts,snippets,templates}/**/*"]
			}),
			tailwind(),
			compression({
				algorithms: [
					defineAlgorithm("gzip", { level: 9 }),
					defineAlgorithm("brotliCompress", {
						params: { [constants.BROTLI_PARAM_QUALITY]: constants.BROTLI_MAX_QUALITY }
					}),
					defineAlgorithm("zstandard", {
						params: { [constants.ZSTD_c_compressionLevel]: 19 }
					})
				],
				exclude: [/\.(png|jpe?g|webp|avif|gif|woff2?)$/]
			}),
			devtoolsJson(),
			kirbyServer(kirbyPort),
			kirbyTypes()
		],
		css: {
			transformer: "lightningcss",
			lightningcss: {
				targets: browserslistToTargets(browserslist(browserslistConfig))
			}
		},
		server: {
			host: "localhost",
			port: Number(env.VITE_DEV_PORT || 3000),
			strictPort: false,
			origin: "",
			allowedHosts: publicUrls.map((url) => new URL(url).hostname),
			proxy: {
				// we proxy anything except the folders our vite dev assets are in
				"^(?!/src|/node_modules|/@vite|/@react-refresh|/assets).*$": {
					target: `http://127.0.0.1:${kirbyPort}`,
					// kirby uses the php server port unless a proxy header is set
					configure: (proxy) =>
						proxy.on("proxyReq", (proxyReq, req) => {
							if (!req.headers["x-forwarded-host"]) proxyReq.setHeader("x-forwarded-host", req.headers.host ?? "")
						})
				}
			}
		}
	}
})
