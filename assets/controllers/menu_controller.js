import { Controller } from "@hotwired/stimulus";

export default class extends Controller {
    static targets = ["megaMenu"];

    show() {
        this.megaMenuTarget.classList.toggle("hidden");
    }
}
