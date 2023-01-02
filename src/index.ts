import './styles/index.scss'

// Remove temporary stylesheet (to prevent FOUC) in development mode
if (import.meta.env.DEV) {
  for (const el of document.querySelectorAll(`[id*="vite-dev"]`)) {
    el.remove()
  }
}

// Auto-load modules
for (const m of Object.values(import.meta.glob('./modules/*.ts', { eager: true }))) {
  ;(m as any).install?.()
}
