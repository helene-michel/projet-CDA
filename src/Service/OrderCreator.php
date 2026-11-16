<?php

namespace App\Service;

use App\Entity\Order;
use App\Entity\OrderLine;
use App\Entity\User;
use App\Repository\PlatformSettingRepository;
use Doctrine\ORM\EntityManagerInterface;

class OrderCreator
{
    public function __construct(
        private Cart $cart,
        private EntityManagerInterface $em,
        private PlatformSettingRepository $settingRepository,
    ) {
    }

    public function createFromCart(User $user): ?Order
    {
        $lines = $this->cart->getLines();
        if (!$lines) {
            return null;
        }

        $commissionRate = $this->settingRepository->get()->getCommissionRate();
        $order = (new Order())->setUser($user);
        foreach ($lines as $line) {
            $order->addLine((new OrderLine())
                ->setProduct($line['product'])
                ->setSeller($line['product']->getUser())
                ->setCommissionRate($commissionRate)
                ->setProductName($line['product']->getName())
                ->setSize($line['size'])
                ->setColorName($line['color']?->getName())
                ->setQuantity($line['quantity'])
                ->setUnitPrice(round($line['total'] / $line['quantity'], 2))
            );
        }

        $this->em->persist($order);
        $this->em->flush();

        return $order;
    }
}
