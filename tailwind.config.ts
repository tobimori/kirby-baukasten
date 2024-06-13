import plugin from 'tailwindcss/plugin'
import defaultTheme from 'tailwindcss/defaultTheme'
import type { Config } from 'tailwindcss'
import formsPlugin from '@tailwindcss/forms'
import { breakpointsString as breakpoints } from "./src/utils/breakpoints"

const variants = plugin(({ addVariant }) => {
  addVariant('not-last', '&:not(:last-child)')
  addVariant('not-first', '&:not(:first-child)')
  addVariant('hocus', ['&:hover', '&:focus-visible'])
  addVariant('group-hocus', ['.group:hover &', '.group:focus-visible &'])
})

export default {
  content: ['./site/**/*.php', './site/**/*.yml', './public/assets/**/*.svg', './src/**/*.ts'],
  future: {
    hoverOnlyWhenSupported: true,
		disableColorOpacityUtilitiesByDefault: true
  },
  theme: {
    fontFamily: {
      sans: ['Inter', ...defaultTheme.fontFamily.sans]
    },
		// convert breakpoints to tailwindcss-object
		screens: Object.entries(breakpoints).reduce((prev, [key, value]) => {
			prev[key] = { max: value }
			prev[`>${key}`] = { min: value }

			return prev
		}, {}),
		container: {
			center: true,
			padding: "2rem",
			screens: {
				_: "96rem"
			}
		},
    extend: {
			spacing: {
				container: "2rem"
			}
		}
  },
  plugins: [variants, formsPlugin]
} satisfies Config
