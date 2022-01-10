<?php

namespace App\Controller\Api\V1;

use App\Entity\Movie;
use App\Form\MovieType;
use App\Repository\MovieRepository;
use App\Service\Slugger;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/api/v1/movies", name="api_v1_movie_")
 */
class MovieController extends AbstractController
{
    /**
     * @Route("", name="browse", methods="GET")
     */
    public function browse(MovieRepository $movieRepo): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');
        $movies = $movieRepo->findAll();
        return $this->json($movies);
    }

    /**
     * @Route("/{id}", name="read", methods="GET")
     */
    public function read(Movie $movie): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');
        return $this->json($movie);
    }

    /**
     * @Route("", name="add", methods="POST")
     */
    public function add(Request $request, EntityManagerInterface $em, Slugger $slugger): Response
    {
        // première méthode qui fonctionne mais on va essayer de faire mieux
        // $infoFromClient = json_decode($request->getContent());

        // $movie = new Movie();
        // $movie->setTitle($infoFromClient->title);

        // $em->persist($movie);
        // $em->flush();

        // on va utiliser un formulaire et son système de validation
        // on récupère les infos fournies en json et on les convertis en tableau
        $infoFromClientAsArray = json_decode($request->getContent(), true);

        // dd($infoFromClientAsArray);

        $movie = new Movie();
        // on créé un formulaire de type Movie
        $form = $this->createForm(MovieType::class, $movie, ['csrf_protection' => false]);
        // on va simuler la soumission du formulaire 
        // pour activer le système de validations des contraintes
        $form->submit($infoFromClientAsArray);

        if ($form->isValid())
        {
            // les contraintes de validation auront été validées
            // dd($movie);

            $em->persist($movie);
            $em->flush();

            // on slugifie après le flush car slugifiMovie a besoin d'un id
            $slugger->slugifyMovie($movie);

            // après ajout ou suppression, on renvoit les données modifiées
            return $this->json($movie);
        }
        else 
        {
            return $this->json((string) $form->getErrors(true, false), Response::HTTP_BAD_REQUEST);
        }
    }
}