<?php

namespace App\DataFixtures;

use App\Entity\User;
use App\Entity\Adress;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserFixtures extends Fixture
{
    public function __construct(
        private UserPasswordHasherInterface $passwordHasher
    ) {
    }

    public function load(ObjectManager $manager): void
    {
        
        $faker = \Faker\Factory::create('fr_FR');
          


        for($i=0 ;$i<=50; $i++){
            $user = new User;
            $user->setPrenom($faker->firstName());
            $user->setNom($faker->lastName());
            $user->setEmail($faker->email());
            $user->setSIRET($faker->siret());
            $user->setRoles(['ROLE_USER', 'ROLE_SELLER', 'ROLE_ADMIN']);
            //$user->setRoles($faker->randomElements(['ROLE_USER', 'ROLE_SELLER', 'ROLE_ADMIN']));
            
            // Mot de passe commun pour les données de test
            $user->setPassword(
                $this->passwordHasher->hashPassword(
                    $user,
                    'Password123!'
                )
            );
            $user->setIsVerified(true);
            

            $adress = new Adress;
            $adress->setAdresse($faker->address());
            $adress->setCP($faker->postcode());
            $adress->setType('principal');

            $user->addAdresse($adress);
            
            $manager->persist($user);
        } 

        $manager->flush();
    }
}
