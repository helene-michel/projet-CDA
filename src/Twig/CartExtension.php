<?php

namespace App\Twig;

use App\Service\Cart;
use Twig\Attribute\AsTwigFunction;

class CartExtension
{
    public function __construct(private Cart $cart)
    {
    }

    #[AsTwigFunction('cart_count')]
    public function cartCount(): int
    {
        return $this->cart->getItemCount();
    }
}
