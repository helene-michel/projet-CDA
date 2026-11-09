import { Controller } from '@hotwired/stimulus';

/*
 * Gestion centralisée des notifications (toasts).
 *
 * 1. Messages flash Symfony ($this->addFlash('success', '...')) :
 *    le conteneur placé dans le layout reçoit app.flashes() via la value "flashes"
 *    et les affiche automatiquement au chargement de la page.
 *
 * 2. Notifications côté front, depuis n'importe quel script :
 *      import { notify } from './notification_controller.js';
 *      notify('Article ajouté', 'success');
 *    ou sans import :
 *      window.dispatchEvent(new CustomEvent('notification:show', {
 *          detail: { message: 'Article ajouté', type: 'success' },
 *      }));
 *
 * 3. Depuis le HTML, via une action Stimulus :
 *      <button data-action="notification#show"
 *              data-notification-message-param="Copié !"
 *              data-notification-type-param="info">
 *    (l'élément doit être à l'intérieur du conteneur, sinon utiliser l'option 2)
 *
 * Types acceptés : success, error / danger, warning, info / infos.
 */

const EVENT_NAME = 'notification:show';

const TYPES = {
    success: { css: 'success', label: 'Succès' },
    error: { css: 'danger', label: 'Erreur' },
    danger: { css: 'danger', label: 'Erreur' },
    warning: { css: 'warning', label: 'Attention' },
    info: { css: 'info', label: 'Information' },
    infos: { css: 'info', label: 'Information' },
    verify_email_error: { css: 'danger', label: 'Erreur' },
};

export function notify(message, type = 'info', options = {}) {
    window.dispatchEvent(new CustomEvent(EVENT_NAME, { detail: { message, type, ...options } }));
}

export default class extends Controller {
    static values = {
        flashes: { type: Object, default: {} },
        duration: { type: Number, default: 5000 },
    };

    connect() {
        this.onNotify = (event) => this.add(event.detail ?? {});
        // Turbo met la page en cache avant de la quitter : on vide les toasts
        // pour ne pas les réafficher lors d'un retour arrière.
        this.onBeforeCache = () => this.clear();

        window.addEventListener(EVENT_NAME, this.onNotify);
        document.addEventListener('turbo:before-cache', this.onBeforeCache);

        Object.entries(this.flashesValue).forEach(([type, messages]) => {
            [].concat(messages).forEach((message) => this.add({ message, type }));
        });
        // Les flashs ne doivent être affichés qu'une seule fois.
        this.flashesValue = {};
    }

    disconnect() {
        window.removeEventListener(EVENT_NAME, this.onNotify);
        document.removeEventListener('turbo:before-cache', this.onBeforeCache);
    }

    // Action Stimulus : data-action="notification#show"
    show({ params }) {
        this.add(params);
    }

    add({ message, type = 'info', duration = this.durationValue }) {
        if (!message) {
            return;
        }

        const config = TYPES[type] ?? TYPES.info;
        const isUrgent = config.css === 'danger' || config.css === 'warning';

        const toast = document.createElement('div');
        toast.className = `notification notification--${config.css}`;
        toast.setAttribute('role', isUrgent ? 'alert' : 'status');

        const body = document.createElement('div');
        body.className = 'notification__body';

        const title = document.createElement('strong');
        title.className = 'notification__title';
        title.textContent = config.label;

        const text = document.createElement('p');
        text.className = 'notification__message';
        text.textContent = message; // textContent : pas d'injection HTML

        const close = document.createElement('button');
        close.type = 'button';
        close.className = 'notification__close';
        close.setAttribute('aria-label', 'Fermer la notification');
        close.innerHTML = '&times;';
        close.addEventListener('click', () => this.dismiss(toast));

        body.append(title, text);
        toast.append(body, close);
        this.element.append(toast);

        // Les erreurs restent affichées jusqu'à fermeture manuelle.
        if (duration > 0 && config.css !== 'danger') {
            toast.timeout = setTimeout(() => this.dismiss(toast), duration);
            toast.addEventListener('mouseenter', () => clearTimeout(toast.timeout));
            toast.addEventListener('mouseleave', () => {
                toast.timeout = setTimeout(() => this.dismiss(toast), duration);
            });
        }
    }

    dismiss(toast) {
        clearTimeout(toast.timeout);
        toast.classList.add('notification--leaving');
        toast.addEventListener('animationend', () => toast.remove(), { once: true });
        // Sécurité si les animations sont désactivées.
        setTimeout(() => toast.remove(), 400);
    }

    clear() {
        this.element.replaceChildren();
    }
}
