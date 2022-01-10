<?php

namespace App\DataFixtures;

use App\Entity\Casting;
use App\Entity\Department;
use App\Entity\Genre;
use App\Entity\Job;
use App\Entity\Movie;
use App\Entity\Person;
use App\Entity\User;
use Faker\Factory;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class AppFixtures extends Fixture
{
    
    public function load(ObjectManager $manager)
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
        for ($i = 0; $i < 15; $i++) 
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
        $castingMovie = $movies[rand(0, count($movies) - 1)];
        $castingPerson = $persons[rand(0, count($persons) - 1)];

        $casting->setPerson( $castingPerson);
        $casting->setMovie($castingMovie);
        $casting->setRole($faker->unique()->character());
        $casting->setCreditOrder((rand(0, 30)));
        
        // persistence en BDD
                
        $manager->persist($casting);
        }

        // ajout job et department

        
        $jobTitlesArray = [
            'SFX engineer',
            'Audio operator',
            'Paint-job',
            'Screenwriter',
            'Cinematographer',
            'Gaffer',
            'Grip',
            'Composer',
            'Showrunner',
            'Production assistant',
            'Choregrapĥer',
            'Production coordinator',
            'Armorer',
            'Art director',
            'Assistant costume designer',
            'Assistant makeup artist',
            'Boom operator',
            'Camera Operator',
            'Colorist',
            'Drone operator',
            'Film electrician',
            'Foley engineer',
            'Hairdresser',
            'Line producer',
            'Post Supervisor',
            'Re-recording mixer',
            'Set decorator',
            'Sound designer',
            'Storyboard artist',
            'Stunt coordinator',
            'Graphic artist',
            'Field recording mixer',
            'Director of photograhy'
        ];

        $departmentsTitlesArray = [
            'SFX',
            'Audio',
            'Painting',
            'Screenplay',
            'Film direction',
            'Filming',
            'Stunts',
            'Medic',
            'Staff',
            'Janitor',
            'dressing'
        ];

        $departments = [] ;
        for ($i = 0; $i < 5; $i++) {
            $department = new Department();
            $department->setName($departmentsTitlesArray[rand(0, count($departmentsTitlesArray) - 1)]);
            $manager->persist($department);
            $departments[] = $department;
        }
        
        for ($i = 0; $i < 30; $i++) 
        {
            $job = new Job();
            $job->setName($jobTitlesArray[rand(0, count($jobTitlesArray) - 1)]);
            $job->setDepartment($departments[rand(0, count($departments) - 1)]);
            $manager->persist($job);
        }

        $user = new User;
        $user->setEmail('lucas.crenais@gmail.com');
        $user->setRoles(["ROLE_ADMIN"]);
        $user->setPassword(
            '$argon2id$v=19$m=65536,t=4,p=1$5tQ3A4p4XCNzwjH6BOhq9Q$mGGfXfWbMIooCW/wn/1F9rKLjCEhG6ms4BroQitTMjk'
            );
        $manager->persist($user);

        $manager->flush();
    }
}
