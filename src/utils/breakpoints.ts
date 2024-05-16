export const toRem = (rem: number) => {
	return rem * Number.parseFloat(getComputedStyle(document.documentElement).fontSize)
}

export const breakpoints = {
	sm: toRem(29.5),
	md: toRem(44),
	lg: toRem(62),
	xl: toRem(80)
}

export const getBreakpointValue = <T>(values: { [K in keyof typeof breakpoints | number | "_"]?: T }) => {
	const viewportWidth = window.innerWidth

	// find the closest breakpoint
	const closestBreakpoint = Object.entries(breakpoints)
		.sort(([, a], [, b]) => b - a)
		.find(([, breakpointValue]) => viewportWidth >= breakpointValue)

	if (closestBreakpoint && Object.hasOwn(values, closestBreakpoint[0])) {
		return values[closestBreakpoint[0] as keyof typeof breakpoints]
	}

	// nothing found yet, check if values has a rem value
	const remEntry = Object.entries(values).find(([key]) => !Number.isNaN(Number(key)))
	if (remEntry) {
		const [remValue, value] = remEntry
		if (viewportWidth >= toRem(Number.parseFloat(remValue))) {
			return value
		}
	}

	// return fallback
	return values._
}
