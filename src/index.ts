import { Core, Transition, Renderer } from '@unseenco/taxi'
import { Application, ControllerConstructor } from '@hotwired/stimulus'
// import 'tachyonjs' // Uncomment this, if you don't want to use Taxi

declare global {
  interface Window {
    Stimulus: Application
    Taxi: Core
  }
}

// Register Stimulus & controllers
window.Stimulus = Application.start()

// Register all controllers in the controllers folder
Object.entries(import.meta.glob('./controllers/*.ts', { eager: true })).forEach(
  ([key, controller]) => {
    window.Stimulus.register(
      key.slice(14, -3),
      (controller as { default: ControllerConstructor }).default
    )
  }
)

if (import.meta.env.DEV) {
  window.Stimulus.debug = true
}

// Install Taxi (remove this if you don't want to use Taxi)
window.Taxi = new Core({
  renderers: Object.entries(import.meta.glob('./renderers/*.ts', { eager: true })).reduce(
    (acc, [key, transition]) => {
      acc[key.slice(12, -3)] = (transition as { default: typeof Renderer }).default
      return acc
    },
    {} as Record<string, typeof Renderer>
  ),
  transitions: Object.entries(import.meta.glob('./transitions/*.ts', { eager: true })).reduce(
    (acc, [key, transition]) => {
      acc[key.slice(14, -3)] = (transition as { default: typeof Transition }).default
      return acc
    },
    {} as Record<string, typeof Transition>
  ),
  allowInterruption: true,
  removeOldContent: false
})
