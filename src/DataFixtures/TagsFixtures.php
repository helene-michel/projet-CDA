<?php

namespace App\DataFixtures;

use App\Entity\Tags;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class TagsFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $tags = [
            'Coton',
            'Lin',
            'Laine',
            'Soie',
            'Velours',
            'Denim',
            'Jean',
            'Satin',
            'Dentelle',
            'Cuir',
            'Similicuir',
            'Viscose',
            'Polyester',
            'Cachemire',
            'Mousseline',
            'Noir',
            'Blanc',
            'Beige',
            'Gris',
            'Bleu',
            'Rouge',
            'Rose',
            'Vert',
            'Jaune',
            'Orange',
            'Violet',
            'Uni',
            'Fleuri',
            'Rayé',
            'À carreaux',
            'Pois',
            'Animalier',
            'Géométrique',
            'Femme',
            'Homme',
            'Enfant',
            'Bébé',
            'Fille',
            'Garçon',
            'Printemps',
            'Été',
            'Automne',
            'Hiver',
            'Mi-saison',
        ];
        foreach($tags as $tagName){
            $tag = new Tags;
            $tag->setNom($tagName);
            $manager->persist($tag);
        }

        $manager->flush();
    }
}
