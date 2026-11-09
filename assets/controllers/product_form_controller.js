import { Controller } from '@hotwired/stimulus';

// Même rendu que format_currency('EUR') côté Twig : "91,20 €"
const priceFormatter = new Intl.NumberFormat('fr-FR', { style: 'currency', currency: 'EUR' });
const MAX_IMAGE_SIZE = 1024 * 1024; // identique à la contrainte Image(maxSize: '1024k')

/*
 * Formulaire produit (espace vendeur) :
 *  - aperçu du prix TTC pendant la saisie (le calcul de référence reste PriceCalculator côté serveur) ;
 *  - aperçu des photos sélectionnées, avec alerte si une photo dépasse 1 Mo.
 */
export default class extends Controller {
    static targets = ['ht', 'tva', 'ttc', 'previews'];

    connect() {
        this.objectUrls = [];
        this.updateTtc();
    }

    disconnect() {
        this.revokePreviews();
    }

    updateTtc() {
        if (!this.hasHtTarget || !this.hasTvaTarget || !this.hasTtcTarget) {
            return;
        }
        const ht = parseFloat(this.htTarget.value.replace(',', '.'));
        const rate = parseFloat(this.tvaTarget.selectedOptions[0]?.dataset.rate);

        if (!Number.isFinite(ht) || ht <= 0 || !Number.isFinite(rate)) {
            this.ttcTarget.textContent = '—';
            return;
        }
        const ttc = Math.round((ht * (1 + rate / 100) + Number.EPSILON) * 100) / 100;
        this.ttcTarget.textContent = `${priceFormatter.format(ttc)} TTC`;
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
