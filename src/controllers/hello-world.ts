import { Controller } from "@hotwired/stimulus"
import { Typed } from "stimulus-typescript"

export default class extends Typed(Controller, {}) {
	connect() {
		console.log("Hello world!")
	}
}
