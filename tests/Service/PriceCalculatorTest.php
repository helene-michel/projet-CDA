<?php

namespace App\Tests\Service;

use App\Entity\Product;
use App\Service\PriceCalculator;
use PHPUnit\Framework\Attributes\DataProvider;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class PriceCalculatorTest extends KernelTestCase
{
    private PriceCalculator $calculator;

    protected function setUp(): void
    {
        $this->calculator = static::getContainer()->get(PriceCalculator::class);
    }

    public static function prices(): iterable
    {
        yield 'TVA 20 %' => [76.0, 20.0, 91.2];
        yield 'arrondi au centime' => [10.99, 20.0, 13.19]; // 13,188
        yield 'TVA réduite 5,5 %' => [10.0, 5.5, 10.55];
        yield 'sans TVA' => [42.0, 0.0, 42.0];
    }

    #[DataProvider('prices')]
    public function testInclVat(float $priceExclVat, float $vatRate, float $expected): void
    {
        self::assertSame($expected, $this->calculator->inclVat($priceExclVat, $vatRate));
    }

    public function testProductPriceInclVat(): void
    {
        $product = (new Product())->setName('Robe')->setPriceExclVat(89.5)->setVatRate(20);

        self::assertSame(107.4, $this->calculator->getPriceInclVat($product));
    }

    public function testProductWithoutVatThrows(): void
    {
        $this->expectException(\LogicException::class);

        $this->calculator->getPriceInclVat((new Product())->setName('Robe')->setPriceExclVat(89.5));
    }
}
