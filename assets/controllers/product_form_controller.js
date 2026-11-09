import { Controller } from '@hotwired/stimulus';

// Même rendu que format_currency('EUR') côté Twig : "91,20 €"
const priceFormatter = new Intl.NumberFormat('fr-FR', { style: 'currency', currency: 'EUR' });
const MAX_IMAGE_SIZE = 1024 * 1024; // same as Image(maxSize: '1024k')

// Seller product form: price incl. VAT preview + photo previews
export default class extends Controller {
    static targets = ['priceExclVat', 'vatRate', 'priceInclVat', 'previews'];

    connect() {
        this.objectUrls = [];
        this.updatePriceInclVat();
    }

    disconnect() {
        this.revokePreviews();
    }

    updatePriceInclVat() {
        const priceExclVat = parseFloat(this.priceExclVatTarget.value.replace(',', '.'));
        const vatRate = parseFloat(this.vatRateTarget.selectedOptions[0]?.dataset.rate);

        if (!Number.isFinite(priceExclVat) || priceExclVat <= 0 || !Number.isFinite(vatRate)) {
            this.priceInclVatTarget.textContent = '—';
            return;
        }
        const priceInclVat = Math.round((priceExclVat * (1 + vatRate / 100) + Number.EPSILON) * 100) / 100;
        this.priceInclVatTarget.textContent = `${priceFormatter.format(priceInclVat)} TTC`;
    }

    previewImages(event) {
        this.revokePreviews();
        this.previewsTarget.replaceChildren();

        Array.from(event.target.files).forEach((file) => {
            const item = document.createElement('li');

            const url = URL.createObjectURL(file);
            this.objectUrls.push(url);
            const img = document.createElement('img');
            img.src = url;
            img.alt = '';
            img.width = 88;
            img.height = 88;

            const caption = document.createElement('span');
            caption.className = 'photo-list__name';
            caption.textContent = file.name;

            item.append(img, caption);

            if (file.size > MAX_IMAGE_SIZE) {
                item.classList.add('is-invalid');
                const warning = document.createElement('span');
                warning.className = 'photo-list__error';
                warning.textContent = 'Trop lourde (1 Mo max.)';
                item.append(warning);
            }

            this.previewsTarget.append(item);
        });
    }

    revokePreviews() {
        this.objectUrls.forEach((url) => URL.revokeObjectURL(url));
        this.objectUrls = [];
    }
}
