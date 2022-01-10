<?php

namespace App\Tests\Service;

use App\Service\Slugger;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class SluggerTest extends KernelTestCase
{
    private $slugger;

    /** 
     * La fonction setUp est appelé par phpunit 
     * avant de lancer les tests
     * c'est un bon moyen d'injecter des dépendances
     */
    protected function setUp(): void
    {
        // ici pour tester notre service Slugger on a besoin de 
        // 1 - étendre de la classe KernelTestCase
        // 2 - démarrer le Kernel de symfony
        // 3 - récupérer le service à tester
        // bonus : on injecte la dépendance grace la la méthode setUp
        self::bootKernel();
        $this->slugger = self::$container->get(Slugger::class);
    }

    public function testSomethingElse()
    {   
        $this->assertTrue(true);
        // $this->assertFalse(true);
    }

    /**
     * @dataProvider slugifierProviderGoodStrings
     */
    public function testGoodValues($testString, $result)
    {

        $this->assertEquals(
            $result,
            $this->slugger->slugify($testString)
        );
    }

    public function slugifierProviderGoodStrings()
    {
        return [
            ['retour Vers le Futur', 'retour-vers-le-futur'],
            ['retour Vers    le Futur', 'retour-vers-le-futur'],
            ['rETOUR VERS LE FUTUR', 'retour-vers-le-futur'],
            ['  retour Vers le Futur   ', 'retour-vers-le-futur'],
            ['  retour-Vers$:* le Futur   ', 'retour-vers-le-futur'],
        ];
    }
}
