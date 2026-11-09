<?php

namespace App\Serializer;

use App\Entity\Products;
use App\Service\PriceCalculator;
use Symfony\Component\Serializer\Normalizer\NormalizerAwareInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerAwareTrait;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

/**
 * Ajoute "prixTtc" au JSON des produits (liste chargée par le contrôleur Stimulus "filter").
 */
class ProductsNormalizer implements NormalizerInterface, NormalizerAwareInterface
{
    use NormalizerAwareTrait;

    private const ALREADY_CALLED = 'products_normalizer_already_called';

    public function __construct(private PriceCalculator $priceCalculator)
    {
    }

    public function normalize(mixed $data, ?string $format = null, array $context = []): array
    {
        // On laisse le normalizer par défaut faire le travail, puis on complète.
        $normalized = $this->normalizer->normalize($data, $format, [self::ALREADY_CALLED => true] + $context);
        $normalized['prixTtc'] = $this->priceCalculator->getPrixTtc($data);

        return $normalized;
    }

    public function supportsNormalization(mixed $data, ?string $format = null, array $context = []): bool
    {
        return $data instanceof Products && !isset($context[self::ALREADY_CALLED]);
    }

    public function getSupportedTypes(?string $format): array
    {
        return [Products::class => false];
    }
}
