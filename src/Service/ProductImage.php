<?php

namespace App\Service;

use App\Entity\Product;
use Symfony\Component\Asset\Packages;

class ProductImage
{
    // Relative to public/
    public const DIRECTORY = 'uploads/productImages';
    private const PLACEHOLDER = 'images/product-default.svg';

    public function __construct(private Packages $packages)
    {
    }

    public function url(Product $product): string
    {
        return $this->urls($product)[0];
    }

    /** @return list<string> placeholder when the product has no photo */
    public function urls(Product $product): array
    {
        $urls = array_map(
            fn (string $filename) => $this->packages->getUrl(self::DIRECTORY.'/'.$filename),
            array_values($product->getImages()),
        );

        return $urls ?: [$this->packages->getUrl(self::PLACEHOLDER)];
    }
}
