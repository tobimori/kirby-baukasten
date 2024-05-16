import { Renderer } from "@unseenco/taxi"
import { lazyLoad } from "unlazy"

export default class extends Renderer {
	initialLoad() {
		lazyLoad()
	}

	onEnterCompleted() {
		this.remove()
		lazyLoad()
	}
}
