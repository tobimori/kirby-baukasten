import plugin from 'tailwindcss/plugin'
import defaultTheme from 'tailwindcss/defaultTheme'
import { Config } from 'tailwindcss'

const variants = plugin(({ addVariant }) => {
  addVariant('not-last', '&:not(:last-child)')
  addVariant('not-first', '&:not(:first-child)')
})

export default {
  content: ['./site/**/*.php', './site/**/*.yml', './public/assets/**/*.svg', './src/**/*.ts'],
  future: {
    hoverOnlyWhenSupported: true
  },
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
  plugins: [variants]
} satisfies Config
