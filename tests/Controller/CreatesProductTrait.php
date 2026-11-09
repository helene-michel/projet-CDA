<?php

namespace App\Tests\Controller;

use App\Entity\Category;
use App\Entity\Product;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;

/**
 * Crée des produits en base de test et les supprime après chaque test
 * (pas de DAMA/transactions dans ce projet).
 */
trait CreatesProductTrait
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

    private function createProduct(string $name = 'Robe "Camille" & co', float $priceExclVat = 89.5, float $vatRate = 20, ?User $owner = null, ?Category $category = null): Product
    {
        $em = static::getContainer()->get(EntityManagerInterface::class);
        $user = $owner ? $em->find(User::class, $owner->getId()) : $this->createUser();
        $newCategory = null === $category;
        $category = $newCategory ? (new Category())->setName('Robes') : $em->find(Category::class, $category->getId());
        $product = (new Product())
            ->setName($name)
            ->setDescription(str_repeat('<b>Lin</b> lavé, coupe ample. ', 20))
            ->setPriceExclVat($priceExclVat)
            ->setVatRate($vatRate)
            ->setImages([])
            ->setCategory($category)
            ->setUser($user);

        foreach ($newCategory ? [$category, $product] : [$product] as $entity) {
            $em->persist($entity);
            $this->created[] = $entity;
        }
        $em->flush();

        return $product;
    }

    private function createUser(?string $firstName = 'Flora', array $roles = []): User
    {
        $em = static::getContainer()->get(EntityManagerInterface::class);
        $user = (new User())
            ->setEmail(uniqid('test-').'@example.com')
            ->setPassword('x')
            ->setLastName('Lefèvre')
            ->setRoles($roles);
        if (null !== $firstName) {
            $user->setFirstName($firstName);
        }
        $em->persist($user);
        $em->flush();
        $this->created[] = $user;

        return $user;
    }

    private function createCategory(string $name = 'Robes'): Category
    {
        $em = static::getContainer()->get(EntityManagerInterface::class);
        $category = (new Category())->setName($name);
        $em->persist($category);
        $em->flush();
        $this->created[] = $category;

        return $category;
    }
}
