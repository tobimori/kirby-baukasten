const defaultTheme = require('tailwindcss/defaultTheme')
const plugin = require('tailwindcss/plugin')

/** @type {import('tailwindcss').Config} */
export default {
  content: {
    files: ["./site/**/*.php", "./site/**/*.yml", "./public/assets/**/*.svg", "./src/index.ts"],
    // transformer for mod() function
    transform: (code) => {
      const variantGroupsRegex = /mod\(.([^,"']+)[^\[]+["'](.+)["']\)/g
      const variantGroupMatches = [...code.matchAll(variantGroupsRegex)]

      variantGroupMatches.forEach(([matchStr, variants, classes]) => {
        const parsedClasses = classes
          .split(" ")
          .map((cls) => `${variants}:${cls}`)
          .join(" ")

        code = code.replaceAll(matchStr, parsedClasses)
      })

      return code
    }
  },
  theme: {
    fontFamily: {
      sans: ['Inter', ...defaultTheme.fontFamily.sans],
    },
    screens: {
      '2xl': { max: '96rem' },
      xl: { max: '80rem' },
      lg: { max: '62rem' },
      md: { max: '44rem' },
      sm: { max: '29.5rem' },
      xs: { max: '22rem' }
    },
    container: {
      center: true
    },
    extend: {}
  },
  plugins: []
}
