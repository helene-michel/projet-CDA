<?php

namespace App\Service;

use App\Entity\Product;

// vatRate is a percentage (20 = 20 %)
class PriceCalculator
{
    public function getPriceInclVat(Product $product): float
    {
        if (null === $product->getPriceExclVat() || null === $product->getVatRate()) {
            throw new \LogicException(\sprintf('Le produit "%s" n\'a pas de prix HT ou de taux de TVA.', $product->getName()));
        }

        return $this->inclVat($product->getPriceExclVat(), $product->getVatRate());
    }

    // Rounded to the cent
    public function inclVat(float $priceExclVat, float $vatRate): float
    {
        return round($priceExclVat * (1 + $vatRate / 100), 2);
    }
}
