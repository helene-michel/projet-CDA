import { Controller } from '@hotwired/stimulus';

// Same output as format_currency('EUR') in Twig
const priceFormatter = new Intl.NumberFormat('fr-FR', { style: 'currency', currency: 'EUR' });

const escapeHtml = (text) => String(text ?? '').replace(/[&<>"']/g, (c) => `&#${c.charCodeAt(0)};`);

export default class extends Controller {
    static values = {
        route: String,
        max: Number,
    }
    static targets = ['materialList', 'materialToggle', 'skeleton', 'results', 'choicePrice', 'count', 'sort']

    connect() {
        // Restore filters from the URL
        const params = new URLSearchParams(window.location.search);
        this.tags = new Set(params.getAll('tags[]'));
        this.price = params.get('price') ?? this.maxValue;
        this.sort = params.get('sort') ?? '';
        this.abortController = null;

        this.choicePriceTarget.value = this.price;
        this.sortTarget.value = this.sort;
        this.element.querySelectorAll('input[type="checkbox"][data-id]').forEach((input) => {
            input.checked = this.tags.has(input.dataset.id);
        });

        this.getProducts();
    }

    disconnect() {
        this.abortController?.abort();
    }

    // ---------- Filters ----------

    filterTag(event) {
        const id = event.currentTarget.dataset.id;
        event.currentTarget.checked ? this.tags.add(id) : this.tags.delete(id);
        this.refresh();
    }

    priceFilter(event) {
        this.price = event.target.value;
        this.refresh();
    }

    orderPrice(event) {
        this.sort = event.target.value;
        this.refresh();
    }

    refresh() {
        window.history.replaceState({}, '', `${window.location.pathname}?${this.buildParams()}`);
        this.getProducts();
    }

    buildParams() {
        const params = new URLSearchParams();
        this.tags.forEach((id) => params.append('tags[]', id));
        params.append('price', this.price);
        params.append('sort', this.sort);
        return params;
    }

    // ---------- Products ----------

    async getProducts() {
        // Cancel the previous request
        this.abortController?.abort();
        this.abortController = new AbortController();

        this.skeletonTarget.classList.remove('d-none');
        this.resultsTarget.classList.add('d-none');

        const url = new URL(this.routeValue);
        url.search = this.buildParams().toString();

        try {
            const response = await fetch(url, { signal: this.abortController.signal });
            if (!response.ok) throw new Error(`Erreur HTTP ${response.status}`);
            this.render(await response.json());
        } catch (error) {
            if (error.name === 'AbortError') return;
            console.error(error);
            this.countTarget.textContent = 'Erreur de chargement';
        }

        this.skeletonTarget.classList.add('d-none');
        this.resultsTarget.classList.remove('d-none');
    }

    render(products) {
        this.countTarget.textContent = `${products.length} résultat${products.length > 1 ? 's' : ''}`;

        if (products.length === 0) {
            this.resultsTarget.innerHTML = '<p class="col-12 text-muted-b">Aucune création ne correspond à ces filtres.</p>';
            return;
        }

        this.resultsTarget.innerHTML = products.map((product) => {
            const url = Routing.generate('app_article', { id: product.id });

            return `
            <div class="col-sm-6 col-lg-4">
                <div class="product-card">
                    <a href="${url}" class="product-img p-bleu d-block">
                        <img src="${escapeHtml(product.imageUrl)}" alt="" loading="lazy">
                    </a>
                    <div class="product-body">
                        <h3 class="product-title"><a href="${url}">${escapeHtml(product.name)}</a></h3>
                        <span class="product-creator">${escapeHtml(product.sellerName)}</span>
                        <span class="product-price">${priceFormatter.format(product.priceInclVat)}</span>
                        <div class="product-footer">
                            <a href="${url}" class="btn-outline-b">Voir le produit</a>
                        </div>
                    </div>
                </div>
            </div>`;
        }).join('');
    }

    // ---------- Materials list ----------

    materialToggle() {
        const expanded = this.materialListTarget.classList.toggle('expanded');
        this.materialToggleTarget.textContent = expanded ? 'Voir moins' : this.materialToggleTarget.dataset.label;
    }

    materialToggleTargetConnected(element) {
        element.dataset.label = element.textContent;
    }

    materialSearch(event) {
        const query = event.target.value.trim().toLowerCase();

        this.materialListTarget.classList.toggle('expanded', query !== '');
        if (this.hasMaterialToggleTarget) {
            this.materialToggleTarget.hidden = query !== '';
        }

        this.materialListTarget.querySelectorAll('.form-check').forEach((item) => {
            const label = item.querySelector('label').textContent.toLowerCase();
            item.style.display = label.includes(query) ? 'block' : 'none';
        });
    }
}
