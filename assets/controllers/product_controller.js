import { Controller } from '@hotwired/stimulus';


export default class extends Controller {
    static targets = ["number", "size"]

    connect() {
        console.log("ready !!!!")
    }

    less() {
        this.numberTarget.value = (this.numberTarget.value == 1) ? 1 : this.numberTarget.value -=1;
    }

    more() {
        this.numberTarget.value++;
    }

    selectSize(e) {
        const elm = e.currentTarget;

        this.sizeTargets.forEach((st) => {
            st.classList.toggle('active', st === elm);
        });
    }
}


/*
document.querySelectorAll(".size-pill").forEach((p) => {
				p.addEventListener("click", () => {
					document
						.querySelectorAll(".size-pill")
						.forEach((s) => s.classList.remove("active"));
					p.classList.add("active");
				});
			});
			document.querySelectorAll(".color-dot").forEach((d) => {
				d.addEventListener("click", () => {
					document
						.querySelectorAll(".color-dot")
						.forEach((c) => c.classList.remove("active"));
					d.classList.add("active");
				});
			});

*/