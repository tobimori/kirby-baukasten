import type { AlpineComponent } from "alpinejs"

export const defineComponent = <T extends Record<string, unknown>, A extends unknown[]>(
	callback: (...args: A) => AlpineComponent<T>
) => callback
