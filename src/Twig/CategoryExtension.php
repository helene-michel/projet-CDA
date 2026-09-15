<?php

namespace App\Twig;

use App\Entity\Category;
use App\Entity\CategoryGroup;
use App\Repository\CategoryGroupRepository;
use Doctrine\ORM\EntityManager;
use Twig\Attribute\AsTwigFilter;
use Doctrine\ORM\EntityManagerInterface;
use Twig\Attribute\AsTwigFunction;

class CategoryExtension {

    public function __construct(private EntityManagerInterface $entityManager) {}

    #[AsTwigFunction('getGroup')]
    function getGroup(){

        return $this->entityManager->getRepository(CategoryGroup::class)->findAll();
    }

    #[AsTwigFunction('getTagsByCategory')]
    function getTagsByCategory(Category $category){

    }
}
