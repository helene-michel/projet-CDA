import { Controller } from '@hotwired/stimulus';

export default class extends Controller {

    static values = {
        route: String,
        csrf: String,
        min: Number,
        max: Number,
    }
    static targets = ["matiereList", "matiereToggle", "skeleton", "results", "choicePrice"]

    connect() {
        // On relit l'URL pour restaurer les filtres (rafraîchissement, lien partagé)
        this.tags = new Set(new URLSearchParams(window.location.search).getAll('tags[]'));
        this.price = new URLSearchParams(window.location.search).get('prix') ?? this.maxValue;
        this.abortController = null;
         this.choicePriceTarget.value = this.price;
        this.syncCheckboxes();
        this.getProducts();
    }

    disconnect() {
        this.abortController?.abort();
    }

    // ---------- Gestion des filtres / URL ----------

    filterTag(event) {
        const elm = event.currentTarget;
        const id = elm.dataset.id;

        if (elm.checked) {
            this.tags.add(id);
        } else {
            this.tags.delete(id);
        }

        this.updateBrowserUrl();
        this.getProducts();
    }
    // ---------- Prix ----------

    priceFilter(event) {
        const elm = event.target;

        const price = elm.value;
        this.price=price;
        this.updateBrowserUrl();
        this.getProducts();
    }
    // Reconstruit les paramètres à partir de l'URL actuelle (les autres paramètres
    // éventuels comme le prix ou le tri sont conservés) et remplace uniquement les tags.
    buildParams() {
        const params = new URLSearchParams(window.location.search);
        params.delete('tags[]');
        params.delete('prix');
        this.tags.forEach(id => params.append('tags[]', id));
        params.append('prix',this.price);
        return params;
    }

    // Met à jour l'URL affichée dans le navigateur, sans recharger la page
    updateBrowserUrl() {
        const query = this.buildParams().toString();
        const newUrl = query
            ? `${window.location.pathname}?${query}`
            : window.location.pathname;
        window.history.replaceState({}, '', newUrl);
    }

    // Coche les cases correspondant aux tags présents dans l'URL
    syncCheckboxes() {
        this.element
            .querySelectorAll('input[type="checkbox"][data-id]')
            .forEach(input => {
                input.checked = this.tags.has(input.dataset.id);
            });
    }

    // ---------- Chargement des produits ----------

    async getProducts() {
        // Annule la requête précédente si l'utilisateur clique vite
        this.abortController?.abort();
        this.abortController = new AbortController();

        this.skeletonTarget.classList.remove('d-none');
        this.resultsTarget.classList.add('d-none');

        const url = new URL(this.routeValue);
        url.search = this.buildParams().toString();

        try {
            const response = await fetch(url.toString(), {
                method: "GET",
                headers: { "X-CSRF-Token": this.csrfValue },
                signal: this.abortController.signal,
            });
            if (!response.ok) throw new Error(`Erreur HTTP ${response.status}`);

            this.card(await response.json());
        } catch (error) {
            // Requête remplacée par une plus récente : on ne touche pas à l'interface
            if (error.name === 'AbortError') return;
            console.error(error);
        }

        this.skeletonTarget.classList.add('d-none');
        this.resultsTarget.classList.remove('d-none');
    }

    card(data) {
        this.resultsTarget.innerHTML = data.map(d => {
            const urlDetailArticle = Routing.generate('app_article', { id: d.id });
            const urlPanierArticle = Routing.generate('app_panier_add', { id: d.id });

            return `
            <div class="col-sm-6 col-lg-4">
                <div class="product-card">
                    <div class="product-img p-bleu">
                        <div class="weave"></div>
                        <span class="badge-fait-main"></span>
                        <a href="#" class="fav" aria-label="Ajouter aux favoris">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                 stroke-width="1.8">
                                <path d="M12 21c4-4 7-7.5 7-11a7 7 0 0 0-14 0c0 3.5 3 7 7 11Z"/>
                            </svg>
                        </a>
                    </div>
                    <div class="product-body">
                        <h3 class="product-title">${d.nom}</h3>
                        <span class="product-creator">${d.user.nom}</span>
                        <span class="product-price">${d.prixHt} €</span>
                        <div class="product-footer">
                            <span class="product-rating">
                                <a href="${urlDetailArticle}" class="btn-outline-b">Détail</a>
                                <a href="${urlPanierArticle}" class="btn-primary-b">Panier</a>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
            `;
        }).join('');
    }

    // ---------- Liste des matières ----------

    matiereToggle() {
        const expanded = this.matiereListTarget.classList.toggle('expanded');
        this.matiereToggleTarget.textContent = expanded ? 'Voir moins' : 'Voir les 44 matières';
    }

    matiereSearch(event) {
        const elm = event.target;
        const query = elm.value.trim().toLowerCase();
        const items = this.matiereListTarget.querySelectorAll('.form-check');

        if (query) {
            this.matiereListTarget.classList.add('expanded');
            this.matiereToggleTarget.style.display = 'none';
        } else {
            this.matiereToggleTarget.style.display = '';
        }

        items.forEach(item => {
            const label = item.querySelector('label').textContent.toLowerCase();
            item.style.display = (!query || label.includes(query)) ? 'block' : 'none';
        });
    }
        // ---------- Ordre des prix ----------
    orderPrice(event){
         const elm = event.target;
         console.log(elm.value);
    }

    
}