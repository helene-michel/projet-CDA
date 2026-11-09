<?php

namespace App\Twig;

use App\Entity\CategoryGroup;
use App\Repository\CategoryGroupRepository;
use Twig\Attribute\AsTwigFunction;

class CategoryExtension
{
    public function __construct(private CategoryGroupRepository $categoryGroupRepository)
    {
    }

    /** @return CategoryGroup[] */
    #[AsTwigFunction('category_groups')]
    public function categoryGroups(): array
    {
        return $this->categoryGroupRepository->findAll();
    }
}
