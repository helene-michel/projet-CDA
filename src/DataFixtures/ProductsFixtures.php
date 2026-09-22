<?php

namespace App\DataFixtures;

use App\Entity\Tags;
use App\Entity\Category;
use App\Entity\Products;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class ProductsFixtures extends Fixture implements DependentFixtureInterface
{

      public function getDependencies():array
      {
        return [
            UserFixtures::class,
            CategoryGroupFixtures::class,
            TagsFixtures::class
        ];
      }

    public function load(ObjectManager $manager): void
    {
        $categories = $manager->getRepository(Category::class)->findAll();
        $tags = $manager->getRepository(Tags::class)->findAll();
        $users = $manager->getRepository(User::class)->findBy([], limit:10);
    
        $countCategories = count($categories);
        $countTags = count($tags);
        
        foreach($users as $user){
            for($i=1;$i<11;$i++) {
                $idProductSelected = rand(1, $countCategories-1);
                $idTagSelected = rand(1, $countTags-1);
            
                $product = new Products();
                $product
                    ->setNom($categories[$idProductSelected]->getNom()." ".$tags[$idTagSelected]->getNom())
                    ->setCategory($categories[$idProductSelected])
                    ->setDescription("Lorem ipsum dolor sit amet, consectetur adipiscing elit. Proin fringilla lacus non diam tincidunt vulputate. Curabitur gravida quam ut volutpat luctus. In eleifend, nulla vel pretium laoreet, ligula est molestie augue, quis hendrerit sem sem quis nunc. Donec imperdiet risus massa, id venenatis lacus feugiat non. Maecenas tortor sapien, faucibus nec mauris ut, ornare fringilla magna. Donec libero metus, mattis ut quam quis, facilisis sollicitudin orci. Vestibulum sed ipsum non neque bibendum interdum. ")
                    ->addTag($tags[$idTagSelected])
                    ->setPrixHt(rand(10, 100))
                    ->setTVA(20)
                    ->setImage([])
                ;
                $user->addProduct($product);
            }
           
        }

        $manager->flush();
    }
}
