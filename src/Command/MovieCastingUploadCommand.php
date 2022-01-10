<?php

namespace App\Command;

use App\Entity\Person;
use App\Repository\MovieRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

class MovieCastingUploadCommand extends Command
{
    protected static $defaultName = 'app:movie:casting-upload';

    private $movieRepository;
    private $em;
    private $parameterBag;

    //! attention il faut lancer le constructeur du parent

    public function __construct(MovieRepository $movieRepository, EntityManagerInterface $em, ParameterBagInterface $parameterBag)
    {
        $this->movieRepository = $movieRepository;
        $this->em = $em;
        $this->parameterBag = $parameterBag;
        parent::__construct();
    }

    protected function configure()
    {
        $this
            ->setDescription('Ajouter les acteurs des films')
            ->addArgument('movieId', InputArgument::OPTIONAL, 'movie Id')
            // ->addOption('option1', null, InputOption::VALUE_NONE, 'Option description')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        // si l'argument movie Id est renseigné
        $movieId = $input->getArgument('movieId');
        if ($movieId) {
            $movie = $this->movieRepository->find($movieId);
            $movies = [$movie];
        }
        else
        {
            // récupérer la liste de tous les movies
            $movies = $this->movieRepository->findAll();
        }

        $key = $this->parameterBag->get('app.omdbapi');
        $omdbApiUrl = "http://www.omdbapi.com/?apikey=".$key."=";

        foreach ($movies as $movie)
        {
                echo 'updating movie ' . $movie->getId() . "\r";;
                $movies = $this->movieRepository->findAll();
                // on gère les espaces
                $titleToSearch = str_replace(' ', '+', $movie->getTitle());
                // demander à omdbapi les informations à propos du film
                $omdbApiResultJson = file_get_contents($omdbApiUrl . $titleToSearch);
                // on transforme le JSON
                $omdbApiResultObj = json_decode($omdbApiResultJson);
                // on peut tester
                // dump($omdbApiResultObj);

                if ($omdbApiResultObj->Response === "True")
                {
                    if ($omdbApiResultObj->Actors != "N/A")
                    {
                        $actors = explode(", ",$omdbApiResultObj->Actors);

                        foreach ($actors as $actor)
                        {
                            echo ' --  ' . $actor . "\r";
                            $person = new Person();
                            $person->setName($actor);
                            $this->em->persist($person);
                        }
                        $this->em->flush();
                    }

                    $io->success('casting ajouté !');
                }
            }

        return Command::SUCCESS;
    }
}
