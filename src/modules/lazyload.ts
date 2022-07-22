import LazyLoad from 'vanilla-lazyload'

export const install = () => {
  new LazyLoad({ elements_selector: '[data-lazyload]' })
}
