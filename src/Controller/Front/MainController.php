<?php

namespace App\Controller\Front;

use App\Repository\MovieRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class MainController extends AbstractController
{
    /**
     * @Route("/", name="homepage")
     */
    public function homepage(MovieRepository $movieRepository): Response
    {
        // afficher la liste de tous les films
        // $movieRepository = $this->getDoctrine()->getRepository(Movie::class);
        
        // on pourrait utiliser une variable intermédiaire avant de passer la liste à la vue
        // $allMovies = $movieRepository->findAll();
        return $this->render('front/main/homepage.html.twig', [
            'movie_list' => $movieRepository->findAllOrderedDQL(),
        ]);
    }
}
