<?php

namespace App\Repository;

use App\Entity\OrderLine;
use App\Entity\OrderStatus;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<OrderLine>
 */
class OrderLineRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, OrderLine::class);
    }

    /**
     * Paid sales per seller (null seller = deleted account).
     *
     * @return list<array{sellerId: ?int, firstName: ?string, lastName: ?string, email: ?string, companyName: ?string, orderCount: int, revenue: float, commission: float, payout: float}>
     */
    public function salesBySeller(): array
    {
        $rows = $this->createQueryBuilder('l')
            ->select('s.id AS sellerId, s.firstName, s.lastName, s.email, sp.companyName')
            ->addSelect('COUNT(DISTINCT o.id) AS orderCount')
            ->addSelect('SUM(l.unitPrice * l.quantity) AS revenue')
            ->addSelect('SUM(l.unitPrice * l.quantity * l.commissionRate / 100) AS commission')
            ->join('l.order', 'o')
            ->leftJoin('l.seller', 's')
            ->leftJoin('s.seller', 'sp')
            ->where('o.status = :paid')
            ->setParameter('paid', OrderStatus::Paid)
            ->groupBy('s.id, sp.companyName')
            ->orderBy('revenue', 'DESC')
            ->getQuery()
            ->getArrayResult();

        return array_map(static function (array $row): array {
            $row['orderCount'] = (int) $row['orderCount'];
            $row['revenue'] = round((float) $row['revenue'], 2);
            $row['commission'] = round((float) $row['commission'], 2);
            $row['payout'] = round($row['revenue'] - $row['commission'], 2);

            return $row;
        }, $rows);
    }
}
