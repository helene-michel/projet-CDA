<?php

namespace App\Repository;

use App\Entity\Order;
use App\Entity\OrderStatus;
use Doctrine\ORM\Query;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Order>
 */
class OrderRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Order::class);
    }

    public function adminQuery(?OrderStatus $status = null, ?string $search = null): Query
    {
        $q = $this->createQueryBuilder('o')
            ->join('o.user', 'u')
            ->addSelect('u')
            ->orderBy('o.orderedAt', 'DESC');

        if ($status) {
            $q->andWhere('o.status = :status')->setParameter('status', $status);
        }

        if ($search) {
            $q->andWhere('u.email LIKE :search OR u.firstName LIKE :search OR u.lastName LIKE :search OR o.id = :id')
                ->setParameter('search', '%'.$search.'%')
                ->setParameter('id', (int) $search);
        }

        return $q->getQuery();
    }

    /**
     * @return array<string, int> status value => count
     */
    public function countByStatus(): array
    {
        $rows = $this->createQueryBuilder('o')
            ->select('o.status AS status, COUNT(o.id) AS total')
            ->groupBy('o.status')
            ->getQuery()
            ->getArrayResult();

        $counts = array_fill_keys(array_column(OrderStatus::cases(), 'value'), 0);
        foreach ($rows as $row) {
            $status = $row['status'] instanceof OrderStatus ? $row['status']->value : $row['status'];
            $counts[$status] = (int) $row['total'];
        }

        return $counts;
    }

}
