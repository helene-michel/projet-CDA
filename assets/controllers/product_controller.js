import { Controller } from '@hotwired/stimulus';

// Quantity stepper
export default class extends Controller {
    static targets = ['quantity'];

    less() {
        this.quantityTarget.value = Math.max(1, Number(this.quantityTarget.value) - 1);
    }

    more() {
        this.quantityTarget.value = Number(this.quantityTarget.value) + 1;
    }
}
