/* main breakpoints definition */
export const breakpoints = {
	xxl: 96,
	xl: 72,
	lg: 62,
	md: 44,
	sm: 29.5
} as const

export const remToPx = (rem: number) => {
	return rem * Number.parseFloat(getComputedStyle(document.documentElement).fontSize)
}

export const breakpointsString = Object.fromEntries(
	Object.entries(breakpoints).map(([key, value]) => [key, `${value}rem` as `${number}rem`])
) as { [key in keyof typeof breakpoints]: `${number}rem` }

export const getBreakpointValue = <T>(values: { [K in keyof typeof breakpoints | number | "_"]?: T }) => {
	const viewportWidth = window.innerWidth

	// find the closest breakpoint
	const closestBreakpoint = Object.entries(breakpoints).find(
		([, breakpointValue]) => viewportWidth >= remToPx(breakpointValue)
	)

	if (closestBreakpoint && Object.hasOwn(values, closestBreakpoint[0])) {
		return values[closestBreakpoint[0] as keyof typeof breakpoints]
	}

	// nothing found yet, check if values has a rem value
	const remEntry = Object.entries(values).find(([key]) => !Number.isNaN(Number(key)))
	if (remEntry) {
		const [remValue, value] = remEntry
		if (viewportWidth >= remToPx(Number.parseFloat(remValue))) {
			return value
		}
	}

	// return fallback
	return values._
}
