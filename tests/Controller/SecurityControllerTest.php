<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class SecurityControllerTest extends WebTestCase
{
    use CreatesProductsTrait;

    public function testLoginErrorIsDisplayedAsNotification(): void
    {
        $client = static::createClient();
        $client->request('GET', '/login');
        self::assertResponseIsSuccessful();
        self::assertSelectorExists('.notification-stack[data-controller="notification"]');
        self::assertSelectorNotExists('nav a.is-connected');
        self::assertSelectorExists('nav a.icon-btn[aria-label="Se connecter"]');

        $client->submitForm('Entrer', [
            '_username' => 'inconnu@example.com',
            '_password' => 'mauvais-mot-de-passe',
        ]);
        self::assertResponseRedirects('/login');
        $crawler = $client->followRedirect();

        $flashes = json_decode(
            $crawler->filter('.notification-stack')->attr('data-notification-flashes-value'),
            true,
        );
        self::assertSame(['Identifiants invalides.'], $flashes['error'] ?? []);
        self::assertSelectorExists('#username.is-invalid');
        self::assertSelectorNotExists('.alert-danger');

        // Le flash n'est affiché qu'une fois : il disparaît au rechargement.
        $client->request('GET', '/login');
        self::assertSelectorNotExists('.notification-stack[data-notification-flashes-value]');
    }

    public function testLoggedInUserSeesWhoIsConnectedInsteadOfTheForm(): void
    {
        $client = static::createClient();
        $user = $this->createUser(prenom: 'Flora');
        $client->loginUser($user);

        $client->request('GET', '/login');

        self::assertResponseIsSuccessful();
        self::assertSelectorTextContains('h1', 'Vous êtes déjà connecté');
        self::assertSelectorTextContains('body', 'Connecté en tant que Flora ('.$user->getEmail().').');
        self::assertSelectorExists('a[href="/logout"]');
        self::assertSelectorExists('a[href="/"]');
        self::assertSelectorNotExists('input[name="_password"]');
        self::assertAnySelectorTextNotContains('body', 'You are logged in');
        self::assertSelectorExists('nav a.icon-btn.is-connected[aria-label="Mon compte, connecté en tant que Flora"]');
    }

    public function testLoggedInUserWithoutFirstNameSeesEmail(): void
    {
        $client = static::createClient();
        $user = $this->createUser(prenom: null);
        $client->loginUser($user);

        $client->request('GET', '/login');

        self::assertSelectorTextContains('body', 'Connecté en tant que '.$user->getEmail().'.');
    }
}
