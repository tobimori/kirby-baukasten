const defaultTheme = require('tailwindcss/defaultTheme')
const plugin = require('tailwindcss/plugin')

/** @type {import('tailwindcss').Config} */
export default {
  content: [],
  theme: {
    fontFamily: {
      sans: ['Inter', ...defaultTheme.fontFamily.sans]
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
