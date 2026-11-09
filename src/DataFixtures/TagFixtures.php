<?php

namespace App\DataFixtures;

use App\Entity\Tag;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class TagFixtures extends Fixture
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
            $tag = new Tag;
            $tag->setName($tagName);
            $manager->persist($tag);
        }

        $manager->flush();
    }
}
