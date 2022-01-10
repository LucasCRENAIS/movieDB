<?php 

namespace App\Service;

use App\Entity\Movie;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\String\Slugger\SluggerInterface;

class Slugger 
{
    private $em;
    private $slugger;

    public function __construct(EntityManagerInterface $em, SluggerInterface $symfonySlugger)
    {
        $this->em = $em;
        $this->slugger = $symfonySlugger;

    }

    public function slugify($toSlugify) 
    {
        return $this->slugger->slug(strtolower($toSlugify));
    }

    public function slugifyMovie(Movie $movie)
    {
        $sluggedTitle = $this->slugify($movie->getTitle());

        // pour gérer les homonymes, on décide d'accoler l'id au slug du film       
        $sluggedTitle .= '' . $movie->getId();
        $movie->setSlug($sluggedTitle);
        
        // j'aimerai bien faire le flush de mon movie dans cette méthode
        $this->em->flush();

        return $movie;
    }

}