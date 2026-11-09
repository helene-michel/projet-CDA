<?php

namespace App\Twig;

use App\Entity\Product;
use App\Service\ProductImage;
use Twig\Attribute\AsTwigFunction;

class ProductExtension
{
    public function __construct(private ProductImage $productImage)
    {
    }

    #[AsTwigFunction('product_image')]
    public function productImage(Product $product): string
    {
        return $this->productImage->url($product);
    }

    /** @return list<string> */
    #[AsTwigFunction('product_images')]
    public function productImages(Product $product): array
    {
        return $this->productImage->urls($product);
    }
}
