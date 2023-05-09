// Default blocks will be loaded lazily
const blocks: { selector: string; file: string }[] = []

// Eager blocks will be included by default in the main bundle
const eager: { selector: string; file: string }[] = []

export const install = () => {
  const modules = Object.fromEntries(
    Object.entries(import.meta.glob('../blocks/*.ts')).map(([key, value]) => [
      key.slice(10, -3),
      value
    ])
  )

  blocks.forEach(({ selector, file }) => {
    const elements = [...document.querySelectorAll<HTMLElement>(selector)]
    if (elements.length === 0) return

    // Auto-load blocks if page contains element
    modules[file]?.().then((m: any) => elements.map((el) => new m.default(el)))
  })

  const eagerModules = Object.fromEntries(
    Object.entries(import.meta.glob('../blocks/eager/*.ts', { eager: true })).map(
      ([key, value]) => [key.slice(16, -3), value]
    )
  ) as any
  eager.forEach(({ selector, file }) => {
    const elements = [...document.querySelectorAll<HTMLElement>(selector)]
    if (elements.length === 0) return
    elements.map((el) => new eagerModules[file].default(el))
  })
}
