<?php

namespace App\Command;

use App\Repository\MovieRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class MoviePosterUploadCommand extends Command
{
    protected static $defaultName = 'app:movie:poster-upload';

    private $movieRepository;
    private $em;

    //! attention il faut lancer le constructeur du parent 

    public function __construct(MovieRepository $movieRepository, EntityManagerInterface $em) 
    {
        $this->movieRepository = $movieRepository;
        $this->em = $em;
        parent::__construct();
    }

    protected function configure()
    {
        $this
            ->setDescription('Ajouter les posters des films')
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

        // ici on pourrait stocker l'api key en tant que paramètre (cad dans services.yaml)
        $omdbApiUrl = "http://www.omdbapi.com/?apikey=8436e9eb&t=";

        foreach ($movies as $movie)
        {

            if (empty($movie->getPoster()))
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
                    // si il n'y a pas d'affiche référecée
                    if ($omdbApiResultObj->Poster != "N/A")
                    {
                        // stocker l'url de l'affiche dans la propriété Poster du Movie
                        echo ' --  ' . $omdbApiResultObj->Poster . "\r"; 
                        $movie->setPoster($omdbApiResultObj->Poster);
                        $this->em->flush();
                    }
                    
                }
            }
        }
        
        $io->success('posters ajoutés !');

        return Command::SUCCESS;
    }
}
