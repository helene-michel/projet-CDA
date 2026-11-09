<?php

namespace App\Tests\Service;

use App\Entity\Products;
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
    public function testTtc(float $ht, float $tva, float $expected): void
    {
        self::assertSame($expected, $this->calculator->ttc($ht, $tva));
    }

    public function testPrixTtcDuProduit(): void
    {
        $product = (new Products())->setNom('Robe')->setPrixHt(89.5)->setTVA(20);

        self::assertSame(107.4, $this->calculator->getPrixTtc($product));
    }

    public function testProduitSansTvaLeveUneException(): void
    {
        $this->expectException(\LogicException::class);

        $this->calculator->getPrixTtc((new Products())->setNom('Robe')->setPrixHt(89.5));
    }
}
