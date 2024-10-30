import plugin from "tailwindcss/plugin"
import type { Config } from "tailwindcss"
import { breakpointsString as breakpoints } from "./src/utils/breakpoints"
import { clampify, createClamper } from "css-clamper"

const variants = plugin(({ addVariant }) => {
	addVariant("not-last", "&:not(:last-child)")
	addVariant("not-first", "&:not(:first-child)")
	addVariant("hocus", ["&:hover", "&:focus-visible"])
	addVariant("group-hocus", [".group:hover &", ".group:focus-visible &"])
})

export const clamp = createClamper(breakpoints.sm, breakpoints.xxl)

export default {
	theme: {
		fontSize: {
			// define fonts here (not in index.css so we can use css-clamper)
			heading: [clamp("2.5rem", "4rem"), { lineHeight: 1.1, fontWeight: 700 }],
			text: ["1rem", { lineHeight: 1.4 }]
		},
		// convert breakpoints to tailwindcss-object
		screens: Object.entries(breakpoints).reduce((prev, [key, value]) => {
			prev[`${key}`] = { max: value }
			prev[`min-${key}`] = { min: value }
			prev[`max-${key}`] = { max: value }

			return prev
		}, {}),
		extend: {
			spacing: {
				container: clampify("1.25rem", "7.25rem", breakpoints.md, breakpoints.xxl)
			}
		}
	},
	plugins: [variants]
} satisfies Config
