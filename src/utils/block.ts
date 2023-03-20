export default class Block {
  el: HTMLElement

  constructor(el?: HTMLElement) {
    if (!el)
      throw new Error('Block element is required, arguments supplied to constructor & super?')
    this.el = el
  }
}
