const blocks: { selector: string; file: string; bundle?: boolean }[] = []

export const install = async () => {
  const modules = Object.fromEntries(
    Object.entries(import.meta.glob('../blocks/*.ts')).map(([key, value]) => [
      key.slice(10, -3),
      value
    ])
  )

  blocks.forEach(async ({ selector, file }) => {
    const elements = [...document.querySelectorAll<HTMLElement>(selector)]
    if (elements.length === 0) return

    // Auto-load blocks if page contains element
    modules[file]?.().then((m) => elements.map((el) => new m.default(el)))
  })
}
