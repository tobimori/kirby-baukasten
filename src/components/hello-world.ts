import { defineComponent } from "@/utils/alpine"

export default defineComponent(() => ({
	message: "Hello World!",
	init() {
		console.log(this.message)
	}
}))
