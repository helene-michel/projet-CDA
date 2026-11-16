<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class PageControllerTest extends WebTestCase
{
    public function testFooterLinksToStaticPages(): void
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/');

        $client->click($crawler->filter('footer a:contains("À propos")')->link());
        self::assertResponseIsSuccessful();
        self::assertSelectorTextContains('h1', 'À propos');

        $client->click($crawler->filter('footer a:contains("Mentions légales")')->link());
        self::assertResponseIsSuccessful();
        self::assertSelectorTextContains('h1', 'Mentions légales');
        self::assertSelectorTextContains('.static-page', 'Hébergement');

        foreach (['Livraison', 'Retours', 'Contact'] as $title) {
            $client->click($crawler->filter('footer a:contains("'.$title.'")')->link());
            self::assertResponseIsSuccessful();
            self::assertSelectorTextContains('h1', $title);
        }
    }

    public function testContactFormRejectsInvalidData(): void
    {
        $client = static::createClient();
        $client->request('GET', '/contact');
        $client->submitForm('Envoyer', [
            'contact[name]' => '',
            'contact[email]' => 'pas-un-email',
            'contact[subject]' => 'Autre question',
            'contact[message]' => 'court',
        ]);

        self::assertResponseStatusCodeSame(422);
        self::assertSelectorTextContains('form', 'Indiquez votre nom.');
        self::assertEmailCount(0);
    }

    public function testContactFormSendsEmail(): void
    {
        $client = static::createClient();
        $client->request('GET', '/contact');
        $client->submitForm('Envoyer', [
            'contact[name]' => 'Camille',
            'contact[email]' => 'camille@example.com',
            'contact[subject]' => 'Une commande',
            'contact[message]' => 'Bonjour, où en est ma commande n° 12 ?',
        ]);

        self::assertResponseRedirects('/contact');
        self::assertEmailCount(1);
        $email = self::getMailerMessage();
        self::assertEmailHeaderSame($email, 'Subject', '[Contact] Une commande');
        self::assertEmailHeaderSame($email, 'Reply-To', 'Camille <camille@example.com>');
        self::assertEmailTextBodyContains($email, 'où en est ma commande n° 12');
    }

    public function testStaticPagesAreInSitemap(): void
    {
        $client = static::createClient();
        $client->request('GET', '/sitemap.xml');

        self::assertStringContainsString('/a-propos</loc>', $client->getResponse()->getContent());
        self::assertStringContainsString('/mentions-legales</loc>', $client->getResponse()->getContent());
        self::assertStringContainsString('/contact</loc>', $client->getResponse()->getContent());
    }
}
