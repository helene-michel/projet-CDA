<?php

namespace App\DataFixtures;

use App\Entity\Category;
use App\Entity\CategoryGroup;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class CategoryGroupFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
       
        $category = [
            'Mode femme'=>[
                'Robes',
                'Jupes',
                'Pantalons',
                'Shorts',
                'bermuda',
                'Combinaisons',
                'Chemises',
                'T-shirts', 
                'Tops',
                'Pulls',
                'Sweats',
                'Vestes',
                'Manteaux',
                'Vêtements de sport',
                'Maillots de bain',
            ],
            'Mode Homme'=>[
                'Pantalons',
                'Shorts',
                'bermuda',
                'Chemises',
                'T-shirts', 
                'Pulls',
                'Sweats',
                'Vestes',
                'Manteaux',
                'Vêtements de sport',
                'Maillots de bain',
            ],
            'Mode enfant'=>[
                'Robes',
                'Jupes',
                'Pantalons',
                'Shorts',
                'bermuda',
                'Combinaisons',
                'Chemises',
                'T-shirts', 
                'Tops',
                'Pulls',
                'Sweats',
                'Vestes',
                'Manteaux',
                'Vêtements de sport',
                'Maillots de bain',
            ],
            'Accessoire'=>[
                'Foulard',
                'Bonnet',
                'bijoux'
            ]
            
        ];
        foreach($category as $group=>$cat){
            $categoryGroup = new CategoryGroup;
            $categoryGroup->setNom($group);
           
            foreach($cat as $c){
                $catEntity = new Category;
                $catEntity->setNom($c);
                $categoryGroup->addCategory($catEntity);
            }
            $manager->persist($categoryGroup);
        }


        $manager->flush();
    }
}
