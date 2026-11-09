<?php

namespace App\Repository;

use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Query;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\PasswordUpgraderInterface;

/**
 * @extends ServiceEntityRepository<User>
 */
class UserRepository extends ServiceEntityRepository implements PasswordUpgraderInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, User::class);
    }

    /**
     * Used to upgrade (rehash) the user's password automatically over time.
     */
    public function upgradePassword(PasswordAuthenticatedUserInterface $user, string $newHashedPassword): void
    {
        if (!$user instanceof User) {
            throw new UnsupportedUserException(sprintf('Instances of "%s" are not supported.', $user::class));
        }

        $user->setPassword($newHashedPassword);
        $this->getEntityManager()->persist($user);
        $this->getEntityManager()->flush();
    }

    public function allUserQuery(?array $roles = [], ?string $search = null): Query
    {
        $q = $this->createQueryBuilder('u')->orderBy('u.id', 'ASC');

        foreach (array_values($roles ?? []) as $i => $role) {
            $q->andWhere('u.roles LIKE :role'.$i)
                ->setParameter('role'.$i, '%"'.$role.'"%');
        }

        if ($search) {
            $q->andWhere('u.email LIKE :search OR u.firstName LIKE :search OR u.lastName LIKE :search')
                ->setParameter('search', '%'.$search.'%');
        }

        return $q->getQuery();
    }

    public function countGroupedByRole(array $rolesToCount): array
    {
        $counts = [];
        foreach ($rolesToCount as $role) {
            $counts[$role] = (int) $this->createQueryBuilder('u')
                ->select('COUNT(u.id)')
                ->andWhere('u.roles LIKE :role')
                ->setParameter('role', '%"' . $role . '"%')
                ->getQuery()
                ->getSingleScalarResult();
        }
        return $counts;
    }

}
