<?php

namespace App\Service;

use Symfony\Component\HttpFoundation\RequestStack;

/**
 * Lecture du panier stocké en session sous la clé "cart" (alimenté par CartController::add).
 */
class Cart
{
    public const SESSION_KEY = 'cart';

    public function __construct(private RequestStack $requestStack)
    {
    }

    /**
     * Nombre total d'articles (somme des quantités).
     */
    public function getItemCount(): int
    {
        $request = $this->requestStack->getMainRequest();
        // Pas de session existante => panier vide. On évite ainsi de démarrer une session
        // (et d'envoyer un cookie) pour chaque visiteur juste pour afficher la navbar.
        if (null === $request || !$request->hasPreviousSession()) {
            return 0;
        }

        $count = 0;
        foreach ($request->getSession()->get(self::SESSION_KEY, []) as $line) {
            $count += max(1, (int) ($line['quantite'] ?? 1));
        }

        return $count;
    }
}
