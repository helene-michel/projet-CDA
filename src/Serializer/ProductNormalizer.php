<?php

namespace App\Serializer;

use App\Entity\Product;
use App\Service\PriceCalculator;
use App\Service\ProductImage;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

// Public product JSON (category list)
class ProductNormalizer implements NormalizerInterface
{
    public function __construct(
        private PriceCalculator $priceCalculator,
        private ProductImage $productImage,
    ) {
    }

    /** @param Product $data */
    public function normalize(mixed $data, ?string $format = null, array $context = []): array
    {
        return [
            'id' => $data->getId(),
            'name' => $data->getName(),
            'priceInclVat' => $this->priceCalculator->getPriceInclVat($data),
            'imageUrl' => $this->productImage->url($data),
            'sellerName' => $data->getUser()?->getDisplayName(),
        ];
    }

    public function supportsNormalization(mixed $data, ?string $format = null, array $context = []): bool
    {
        return $data instanceof Product;
    }

    public function getSupportedTypes(?string $format): array
    {
        return [Product::class => true];
    }
}
