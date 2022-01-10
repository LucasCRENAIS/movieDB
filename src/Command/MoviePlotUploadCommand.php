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
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

class MoviePlotUploadCommand extends Command
{
    protected static $defaultName = 'app:movie:plot-upload';
    //! attention il faut lancer le constructeur du parent 

    private $movieRepository;
    private $em;
    private $parameterBag;

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
            ->setDescription('a command to upload film plots')
            // ->addArgument('arg1', InputArgument::OPTIONAL, 'Argument description')
            // ->addOption('option1', null, InputOption::VALUE_NONE, 'Option description')
        ;
    }

    
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $key = $this->parameterBag->get('app.omdbapi');
        $omdbApiUrl = "http://www.omdbapi.com/?apikey=".$key."=";

        $io = new SymfonyStyle($input, $output);

        $movies = $this->movieRepository->findAll();

        foreach ($movies as $movie)

        {
            if (empty($movie->getPlot()))             
            {
                echo 'updating plots ' . $movie->getId() . "\r";; 
                // on gère les espaces
                $titleToSearch = str_replace(' ', '+', $movie->getTitle());
                // demander à omdbapi les informations à propos du film
                $omdbApiResultJson = file_get_contents($omdbApiUrl . $titleToSearch);
                // on transforme le JSON
                $omdbApiResultObj = json_decode($omdbApiResultJson);
                // on peut tester
                //  dump($omdbApiResultObj);

                if ($omdbApiResultObj->Response === "True")
                {
                    $io->success('résumés ajoutés');

                    // si il n'y a pas de résumé référencé
                    if ($omdbApiResultObj->Plot != "N/A")
                    {
                        // stocker l'url du résumé dans la propriété Plot du Movie
                        echo ' --  ' . $omdbApiResultObj->Plot . "\r"; 
                        $movie->setPlot($omdbApiResultObj->Plot);
                        $this->em->flush();
                    }
                    
                }
            }
    
        }
        return Command::SUCCESS;
    }
}
