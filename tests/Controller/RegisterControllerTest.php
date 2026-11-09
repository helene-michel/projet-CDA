<?php

namespace App\Tests\Controller;

use App\Entity\User;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class RegisterControllerTest extends WebTestCase
{
    use CreatesProductTrait;

    public function testRegisterAsSeller(): void
    {
        $client = static::createClient();
        $email = uniqid('register-').'@example.com';

        $crawler = $client->request('GET', '/creation_compte');
        $client->submit($crawler->filter('form[name="user"]')->form([
            'user[firstName]' => 'Flora',
            'user[lastName]' => 'Lefèvre',
            'user[email]' => $email,
            'user[plainPassword][first]' => 'Password123!',
            'user[plainPassword][second]' => 'Password123!',
            'user[isSeller]' => true,
        ]));

        self::assertResponseRedirects();
        $user = static::getContainer()->get(UserRepository::class)->findOneBy(['email' => $email]);
        $this->created[] = $user;
        self::assertContains('ROLE_SELLER', $user->getRoles());
        self::assertNotSame('Password123!', $user->getPassword());
        self::assertSame('Flora', $user->getFirstName());
    }

    public function testPasswordConfirmationMustMatch(): void
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/creation_compte');
        $client->submit($crawler->filter('form[name="user"]')->form([
            'user[firstName]' => 'Flora',
            'user[lastName]' => 'Lefèvre',
            'user[email]' => uniqid('register-').'@example.com',
            'user[plainPassword][first]' => 'Password123!',
            'user[plainPassword][second]' => 'autre-chose',
        ]));

        self::assertResponseStatusCodeSame(422);
        self::assertSelectorTextContains('form', 'ne correspondent pas');
    }
}
