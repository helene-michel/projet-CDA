import { Controller } from '@hotwired/stimulus';

export default class extends Controller {
    static targets = ['main', 'thumb'];

    select(event) {
        this.mainTarget.src = event.currentTarget.dataset.src;
        this.thumbTargets.forEach((thumb) => thumb.classList.toggle('active', thumb === event.currentTarget));
    }
}
