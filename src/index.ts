import { Application, ControllerConstructor } from '@hotwired/stimulus'
import 'tachyonjs'
import { lazyLoad } from 'unlazy'

declare global {
  interface Window {
    Stimulus: Application
  }
}

// Register Stimulus & controllers
window.Stimulus = Application.start()

// Register all controllers in the controllers folder
Object.entries(import.meta.glob('./controllers/*.ts', { eager: true }))
  .map(
    ([key, value]) =>
      [key.slice(14, -3) as string, value as { default: ControllerConstructor }] as const
  )
  .forEach(([key, controller]) => {
    window.Stimulus.register(key as string, controller.default)
  })

if (import.meta.env.DEV) {
  window.Stimulus.debug = true
}

// Start lazyload
lazyLoad()
