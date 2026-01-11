import focus from "@alpinejs/focus"
import Alpine from "alpinejs"
import "htmx.org" // TODO: figure out if we can replace htmx.org with alpine-ajax
import "idiomorph/dist/idiomorph-ext.js"
import { lazyLoad } from "unlazy"

import.meta.glob(["../assets/**"]) // Import all assets for copying them to dist

console.log(
	"%cMade with Kirby and ❤️ by Love & Kindness GmbH",
	"font-size: 12px; font-weight: bold; color: #fff; background-color: #000; padding: 8px 12px; margin: 4px 0; border-radius: 4px;"
)

declare global {
	interface Window {
		Alpine: typeof Alpine
	}
}

// Register Alpine
window.Alpine = Alpine

Alpine.plugin(focus)

for (const [path, module] of Object.entries(import.meta.glob("./components/*.ts", { eager: true }))) {
	const name = path.slice(13, -3)
	// convert kebab-case to camelCase
	const camelName = name.replace(/-([a-z])/g, (_, letter) => letter.toUpperCase())
	Alpine.data(camelName, (module as { default: any }).default)
}

Alpine.start()
lazyLoad('[loading="lazy"][data-src]')
