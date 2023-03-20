export { default as Block } from './block'
export {breakpoints, getBreakpointValue, toRem} from './breakpoints'

/**
 * Returns the first element that is a descendant of node that
 * matches selectors, but throws an Error when none is found.
 *
 * @param selector - A CSS selector to match against.
 * @param parent - The node to search within.
 * @returns The first matching element.
 */
export const rQS = <T extends Element>(selector: string, parent: Element = document.body): T => {
  const el = parent.querySelector<T>(selector)
  if (!el) throw new Error(`Element ${selector} not found`)
  return el
}
