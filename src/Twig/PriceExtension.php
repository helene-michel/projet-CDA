<?php

namespace App\Twig;

use App\Entity\Products;
use App\Service\PriceCalculator;
use Twig\Attribute\AsTwigFunction;

class PriceExtension
{
    public function __construct(private PriceCalculator $priceCalculator)
    {
    }

    /**
     * Prix TTC (nombre). Pour l'afficher : {{ prix_ttc(produit)|format_currency('EUR') }}.
     */
    #[AsTwigFunction('prix_ttc')]
    public function prixTtc(Products $product): float
    {
        return $this->priceCalculator->getPrixTtc($product);
    }
}
