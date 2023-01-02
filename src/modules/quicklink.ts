import { listen } from 'quicklink'

export const install = () => {
  if (!import.meta.env.DEV) {
    listen()
  }
}
