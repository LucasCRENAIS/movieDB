<?php

namespace App\Tests\Front;

use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class MainControllerTest extends WebTestCase
{
    public function testHomePageIsUp()
    {
        // un "faux navigateur" utilisé pour les tests
        $client = static::createClient();
        // le crawler va nous permettre d'accéder au contenu html de la réponse
        $crawler = $client->request('GET', '/');
        
        $this->assertResponseIsSuccessful();

        $this->assertSelectorTextContains('nav a.btn', 'Connexion', 'le bouton Connexion est manquant');

        // $this->assertSelectorTextContains('h1', 'Hello World');
    }

    public function testAdminNavigation()
    {
        $client = static::createClient();

        // connectons un utilisateur de notre BDD de test
        $userRepository = static::$container->get(UserRepository::class);

        // retrieve the test user
        $testUser = $userRepository->findOneByEmail('lucas.crenais@gmail.com');

        // simulate $testUser being logged in
        $client->loginUser($testUser);

        //ici on est connecté en tant que lucas.crenais@gmail.com
        // les urls auxquels on accède prendront cela en compte
        $crawler = $client->request('GET', '/');
        
        $this->assertResponseIsSuccessful();

        $this->assertSelectorTextContains('nav #navbarCollapse ul li:last-child', 'Users', 'En admin on ne voit pas Utilisateur !');
        $this->assertSelectorNotExists('nav #navbarCollapse ul li:nth-child(8)', 'Il n\'y a que 4 éléments dans le menu');

    }
}
