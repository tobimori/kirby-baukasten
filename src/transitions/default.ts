import { Transition } from '@unseenco/taxi'

export default class extends Transition {
  onEnter({ done }: { done: Function }) {
    window.scrollTo({
      top: 0,
      left: 0,
      behavior: 'instant'
    })

    done()
  }
}
