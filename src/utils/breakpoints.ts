export const toRem = (rem: number) => {
  return rem * parseFloat(getComputedStyle(document.documentElement).fontSize)
}

export const breakpoints: Record<string, number> = {
  sm: toRem(29.5),
  md: toRem(44),
  lg: toRem(62),
  xl: toRem(80)
}

export const getBreakpointValue = (values: { [breakpoint: string | number]: any; _: any }) => {
  let returnValue = values._
  let lastBreakpoint: number

  Object.entries(values).forEach(([breakpoint, value]) => {
    let computedBreakpoint = toRem(Number(breakpoint))
    if (breakpoint in breakpoints) {
      computedBreakpoint = breakpoints?.[breakpoint]
    }

    if (lastBreakpoint < computedBreakpoint) {
      return
    }

    if (window.innerWidth <= computedBreakpoint) {
      returnValue = value
    }
  })

  return returnValue
}
