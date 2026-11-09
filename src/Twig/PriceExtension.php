<?php

namespace App\Twig;

use App\Entity\Product;
use App\Service\PriceCalculator;
use Twig\Attribute\AsTwigFunction;

class PriceExtension
{
    public function __construct(private PriceCalculator $priceCalculator)
    {
    }

    /**
     * Prix TTC (nombre). Pour l'afficher : {{ price_incl_vat(produit)|format_currency('EUR') }}.
     */
    #[AsTwigFunction('price_incl_vat')]
    public function priceInclVat(Product $product): float
    {
        return $this->priceCalculator->getPriceInclVat($product);
    }
}
