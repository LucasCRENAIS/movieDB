<?php

namespace App\Controller\Back;

use App\Entity\Casting;
use App\Entity\Genre;
use App\Entity\Movie;
use App\Entity\Person;
use App\Repository\GenreRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Faker\Factory;


class SandboxController extends AbstractController
{
    
    /**
     * @Route("/sandbox/genres", name="sandbox_genre")
     */
    public function demoGenre(GenreRepository $repo)
    {
        $genres = $repo->findGenreDemo('Comédie');

        dump($genres);

        return $this->render('back/sandbox/index.html.twig', [
            'controller_name' => 'SandboxController',
        ]);
    }


    /**
     * @Route("/sandbox/db_init", name="sandbox_init")
     */
    public function db_init(EntityManagerInterface $manager): Response
    {
               // on récupère l'entity manager dans le code
        // $entityManager = $this->getDoctrine()->getManager();
        
        $faker = Factory::create('fr_FR');
        $faker->addProvider(new \Xylis\FakerCinema\Provider\Movie($faker));
        $faker->addProvider(new \Xylis\FakerCinema\Provider\Character($faker));


        // Ajoutons des genres
        $genres = [];
        for ($i = 0; $i < 10; $i++) 
        {
            $genre = new Genre();
            $genre->setName($faker->unique()->movieGenre());
            $manager->persist($genre);
            $genres[] = $genre;
        }
        
        // Ajoutons des films        
        $movies = [];
        for ($i = 0; $i < 10; $i++) 
        {
            $movie = new Movie();
            $movie->setTitle($faker->unique()->movie());

            // on définit par film un nb de genres à ajouter
            $nbGenreToAdd = rand(2, 4);
            
            $keysToAdd = array_rand($genres, $nbGenreToAdd);
            // array_rand renvoi soit une clef soit un tableau
            // dans le cas ou on recoit une clef, on en fait un tableau pour que notre boucle fonctionne
            if (! is_array($keysToAdd))
            {
                $keysToAdd = [$keysToAdd];
            }
            foreach ($keysToAdd as $key)
            {
                // on associe ce genre à notre movie
                $movie->addGenre($genres[$key]);
            }    

            $manager->persist($movie);
            $movies[] = $movie;
        }

        // Ajoutons des personnes

        $persons = [];
        for ($i = 0; $i < 30; $i++) 
        {
            $person = new Person();
            // ->unique() permet d'éviter les doublons
            $person->setName($faker->unique()->name());
            $manager->persist($person);
            $persons[] = $person;
        }

        // casting
       
        for ($i = 0; $i < 30; $i++)
        {
        $casting = new Casting();
        $castingMovie = $movies[rand(1, count($movies) - 1)];;
        $castingPerson = $persons[rand(1, count($persons) - 1)];;

        $casting->setPerson( $castingPerson);
        $casting->setMovie($castingMovie);
        $casting->setRole($faker->unique()->character());
        $casting->setCreditOrder((rand(1, 30)));
        
        // persistence en BDD
                
        $manager->persist($casting);
        }
        
        // permet d'exécuter les requetes dans la BDD
        $manager->flush();

        return $this->render('sandbox/index.html.twig', [
            'controller_name' => 'SandboxController',
        ]);
    }
}
