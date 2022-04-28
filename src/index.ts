import Loadeer from 'loadeer'
import { listen } from 'quicklink'

import './styles/index.scss'

// Remove temporary stylesheet (to prevent FOUC) in development mode
if (import.meta.env.DEV) {
  for (const el of document.querySelectorAll(`[id*="vite-dev"]`)) {
    el.remove()
  }
}

// Auto-load modules
for (const m of Object.values(import.meta.globEager('./modules/*.ts'))) {
  m.install?.()
}

// Auto-load templates
const templates = Object.fromEntries(
  Object.entries(import.meta.glob('./templates/*.ts')).map(([key, value]) => [
    key.slice(12, -3),
    value
  ])
)

templates[document.body.dataset.template ?? '']?.().then((m) => m.default?.())

const loadeer = new Loadeer()
loadeer.observe()

if (!import.meta.env.DEV) {
  listen()
}
