<?php

namespace App\Tests\Controller;

use App\Entity\Category;
use App\Entity\Products;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;

/**
 * Crée des produits en base de test et les supprime après chaque test
 * (pas de DAMA/transactions dans ce projet).
 */
trait CreatesProductsTrait
{
    /** @var object[] */
    private array $created = [];

    #[\PHPUnit\Framework\Attributes\After]
    protected function removeCreatedEntities(): void
    {
        if (!$this->created) {
            return;
        }
        $em = static::getContainer()->get(EntityManagerInterface::class);
        foreach (array_reverse($this->created) as $entity) {
            // L'entité a pu être supprimée par le test lui-même (ex. : suppression d'un produit).
            if ($managed = $em->find($entity::class, $entity->getId())) {
                $em->remove($managed);
            }
        }
        $em->flush();
        $this->created = [];
        // Ce hook s'exécute après tearDown() : on ne laisse pas un kernel démarré au test suivant.
        static::ensureKernelShutdown();
    }

    private function createProduct(string $nom = 'Robe "Camille" & co', float $prixHt = 89.5, float $tva = 20, ?User $owner = null, ?Category $category = null): Products
    {
        $em = static::getContainer()->get(EntityManagerInterface::class);
        $user = $owner ? $em->find(User::class, $owner->getId()) : $this->createUser();
        $newCategory = null === $category;
        $category = $newCategory ? (new Category())->setNom('Robes') : $em->find(Category::class, $category->getId());
        $product = (new Products())
            ->setNom($nom)
            ->setDescription(str_repeat('<b>Lin</b> lavé, coupe ample. ', 20))
            ->setPrixHt($prixHt)
            ->setTVA($tva)
            ->setImage([])
            ->setCategory($category)
            ->setUser($user);

        foreach ($newCategory ? [$category, $product] : [$product] as $entity) {
            $em->persist($entity);
            $this->created[] = $entity;
        }
        $em->flush();

        return $product;
    }

    private function createUser(?string $prenom = 'Flora', array $roles = []): User
    {
        $em = static::getContainer()->get(EntityManagerInterface::class);
        $user = (new User())
            ->setEmail(uniqid('test-').'@example.com')
            ->setPassword('x')
            ->setNom('Lefèvre')
            ->setRoles($roles);
        if (null !== $prenom) {
            $user->setPrenom($prenom);
        }
        $em->persist($user);
        $em->flush();
        $this->created[] = $user;

        return $user;
    }

    private function createCategory(string $nom = 'Robes'): Category
    {
        $em = static::getContainer()->get(EntityManagerInterface::class);
        $category = (new Category())->setNom($nom);
        $em->persist($category);
        $em->flush();
        $this->created[] = $category;

        return $category;
    }
}
