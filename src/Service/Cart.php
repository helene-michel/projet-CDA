<?php

namespace App\Service;

use App\Entity\Color;
use App\Entity\Product;
use App\Repository\ColorRepository;
use App\Repository\ProductRepository;
use Symfony\Component\HttpFoundation\RequestStack;

// Session cart: list of ['productId', 'size', 'colorId', 'quantity']
class Cart
{
    public const SESSION_KEY = 'cart';

    public function __construct(
        private RequestStack $requestStack,
        private ProductRepository $productRepository,
        private ColorRepository $colorRepository,
        private PriceCalculator $priceCalculator,
    ) {
    }

    public function add(Product $product, ?string $size, ?int $colorId, int $quantity): void
    {
        $items = $this->getItems();
        $items[] = [
            'productId' => $product->getId(),
            'size' => $size,
            'colorId' => $colorId,
            'quantity' => max(1, $quantity),
        ];
        $this->requestStack->getSession()->set(self::SESSION_KEY, $items);
    }

    public function remove(int $index): void
    {
        $items = $this->getItems();
        unset($items[$index]);
        $this->requestStack->getSession()->set(self::SESSION_KEY, array_values($items));
    }

    public function getItemCount(): int
    {
        // No session yet: don't start one just for the navbar
        if (!$this->requestStack->getMainRequest()?->hasPreviousSession()) {
            return 0;
        }

        return array_sum(array_column($this->getItems(), 'quantity'));
    }

    /**
     * @return list<array{index: int, product: Product, size: ?string, color: ?Color, quantity: int, total: float}>
     */
    public function getLines(): array
    {
        $lines = [];
        foreach ($this->getItems() as $index => $item) {
            $product = $this->productRepository->find($item['productId']);
            if (!$product) {
                continue;
            }
            $lines[] = [
                'index' => $index,
                'product' => $product,
                'size' => $item['size'],
                'color' => $item['colorId'] ? $this->colorRepository->find($item['colorId']) : null,
                'quantity' => $item['quantity'],
                'total' => $this->priceCalculator->getPriceInclVat($product) * $item['quantity'],
            ];
        }

        return $lines;
    }

    public function getTotal(): float
    {
        return array_sum(array_column($this->getLines(), 'total'));
    }

    private function getItems(): array
    {
        return $this->requestStack->getSession()->get(self::SESSION_KEY, []);
    }
}
