<?php

namespace App\DataFixtures;

use App\Entity\User;
use App\Entity\Seller;
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
            //$user->setRoles(['ROLE_USER', 'ROLE_SELLER', 'ROLE_ADMIN']);
            $roles = [['ROLE_USER'],['ROLE_USER','ROLE_SELLER'], ['ROLE_USER','ROLE_ADMIN']];
            $user->setRoles($roles[rand(0,count($roles)-1)]);
            
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

            if(in_array('ROLE_SELLER', $user->getRoles())){
                $seller = new Seller;
                
                $seller->setSIRET($faker->siret());
                $seller->setDescription("Lorem ipsum dolor sit amet, consectetur adipiscing elit. 
                Proin fringilla lacus non diam tincidunt vulputate. Curabitur gravida quam ut volutpat
                luctus. In eleifend, nulla vel pretium laoreet, ligula est molestie augue, quis hendrerit 
                sem sem quis nunc. Donec imperdiet risus massa, id venenatis lacus feugiat non. Maecenas 
                tortor sapien, faucibus nec mauris ut, ornare fringilla magna. Donec libero metus, mattis
                ut quam quis, facilisis sollicitudin orci. Vestibulum sed ipsum non neque bibendum interdum. ");
                $seller->setRaisonSocial($user->getPrenom().'cie');
                $user->setSeller($seller);
            }             
               
             
            $manager->persist($user);
        } 

        $user = new User;
        $user->setPrenom('Helene');
        $user->setNom('MICHEL');
        $user->setEmail('helenemichel@gmail.com');
        $user->setRoles(['ROLE_USER','ROLE_ADMIN']);
        
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

        $manager->flush();
    }
}
