<?php

namespace App\Service;

use App\Entity\Products;

/**
 * Calcul des prix. Products::$TVA contient un taux en pourcentage (20 = 20 %).
 */
class PriceCalculator
{
    public function getPrixTtc(Products $product): float
    {
        if (null === $product->getPrixHt() || null === $product->getTVA()) {
            throw new \LogicException(\sprintf('Le produit "%s" n\'a pas de prix HT ou de taux de TVA.', $product->getNom()));
        }

        return $this->ttc($product->getPrixHt(), $product->getTVA());
    }

    /**
     * Arrondi au centime (arrondi commercial).
     */
    public function ttc(float $prixHt, float $tauxTva): float
    {
        return round($prixHt * (1 + $tauxTva / 100), 2);
    }
}
