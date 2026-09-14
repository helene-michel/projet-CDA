<?php

namespace App\DataFixtures;

use App\Entity\User;
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
        $users = [
    ['Martin', 'Emma', 'emma.martin@example.test', '73282932000001', 'ROLE_USER'],
    ['Bernard', 'Lucas', 'lucas.bernard@example.test', '73282932000002', 'ROLE_USER'],
    ['Dubois', 'Chloé', 'chloe.dubois@example.test', '73282932000003', 'ROLE_USER'],
    ['Thomas', 'Hugo', 'hugo.thomas@example.test', '73282932000004', 'ROLE_USER'],
    ['Robert', 'Camille', 'camille.robert@example.test', '73282932000005', 'ROLE_USER'],
    ['Richard', 'Louis', 'louis.richard@example.test', '73282932000006', 'ROLE_USER'],
    ['Petit', 'Manon', 'manon.petit@example.test', '73282932000007', 'ROLE_USER'],
    ['Durand', 'Nathan', 'nathan.durand@example.test', '73282932000008', 'ROLE_USER'],
    ['Leroy', 'Jade', 'jade.leroy@example.test', '73282932000009', 'ROLE_USER'],
    ['Moreau', 'Gabriel', 'gabriel.moreau@example.test', '73282932000010', 'ROLE_USER'],

    ['Simon', 'Clara', 'clara.simon@example.test', '73282932000011', 'ROLE_SELLER'],
    ['Laurent', 'Arthur', 'arthur.laurent@example.test', '73282932000012', 'ROLE_SELLER'],
    ['Lefebvre', 'Alice', 'alice.lefebvre@example.test', '73282932000013', 'ROLE_SELLER'],
    ['Michel', 'Thomas', 'thomas.michel@example.test', '73282932000014', 'ROLE_SELLER'],
    ['Garcia', 'Élodie', 'elodie.garcia@example.test', '73282932000015', 'ROLE_SELLER'],
    ['David', 'Maxime', 'maxime.david@example.test', '73282932000016', 'ROLE_SELLER'],
    ['Bertrand', 'Sarah', 'sarah.bertrand@example.test', '73282932000017', 'ROLE_SELLER'],
    ['Roux', 'Julien', 'julien.roux@example.test', '73282932000018', 'ROLE_SELLER'],
    ['Vincent', 'Laura', 'laura.vincent@example.test', '73282932000019', 'ROLE_SELLER'],
    ['Fournier', 'Antoine', 'antoine.fournier@example.test', '73282932000020', 'ROLE_SELLER'],
    ['Morel', 'Léa', 'lea.morel@example.test', '73282932000021', 'ROLE_SELLER'],
    ['Girard', 'Mathieu', 'mathieu.girard@example.test', '73282932000022', 'ROLE_SELLER'],
    ['André', 'Sophie', 'sophie.andre@example.test', '73282932000023', 'ROLE_SELLER'],
    ['Lefort', 'Romain', 'romain.lefort@example.test', '73282932000024', 'ROLE_SELLER'],
    ['Gautier', 'Julie', 'julie.gautier@example.test', '73282932000025', 'ROLE_SELLER'],

    ['Bonnet', 'Nicolas', 'nicolas.bonnet@example.test', '73282932000026', 'ROLE_USER'],
    ['Dupont', 'Anaïs', 'anais.dupont@example.test', '73282932000027', 'ROLE_USER'],
    ['Lambert', 'Paul', 'paul.lambert@example.test', '73282932000028', 'ROLE_USER'],
    ['Fontaine', 'Margaux', 'margaux.fontaine@example.test', '73282932000029', 'ROLE_USER'],
    ['Rousseau', 'Alexandre', 'alexandre.rousseau@example.test', '73282932000030', 'ROLE_USER'],
    ['Blanc', 'Louise', 'louise.blanc@example.test', '73282932000031', 'ROLE_USER'],
    ['Guerin', 'Baptiste', 'baptiste.guerin@example.test', '73282932000032', 'ROLE_USER'],
    ['Muller', 'Océane', 'oceane.muller@example.test', '73282932000033', 'ROLE_USER'],
    ['Henry', 'Valentin', 'valentin.henry@example.test', '73282932000034', 'ROLE_USER'],
    ['Marchand', 'Inès', 'ines.marchand@example.test', '73282932000035', 'ROLE_USER'],

    ['Chevalier', 'Thomas', 'thomas.chevalier@example.test', '73282932000036', 'ROLE_ADMIN'],
    ['Robin', 'Marion', 'marion.robin@example.test', '73282932000037', 'ROLE_ADMIN'],
    ['Masson', 'Pierre', 'pierre.masson@example.test', '73282932000038', 'ROLE_ADMIN'],
    ['Henry', 'Claire', 'claire.henry@example.test', '73282932000039', 'ROLE_ADMIN'],
    ['Legrand', 'Sébastien', 'sebastien.legrand@example.test', '73282932000040', 'ROLE_ADMIN'],
    ['Perrin', 'Julie', 'julie.perrin@example.test', '73282932000041', 'ROLE_ADMIN'],
];

        foreach ($users as [$prenom, $nom, $email,$siret,$role]) {
            $user = new User();

            $user->setPrenom($prenom);
            $user->setNom($nom);
            $user->setEmail($email);
            $user->setSIRET($siret);
            $user->setRoles([$role]);

            // Mot de passe commun pour les données de test
            $user->setPassword(
                $this->passwordHasher->hashPassword(
                    $user,
                    'Password123!'
                )
            );
            $user->setIsVerified(true);
            $manager->persist($user);
        }

        $manager->flush();
    }
}
