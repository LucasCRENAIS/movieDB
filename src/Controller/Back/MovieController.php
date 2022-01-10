<?php

namespace App\Controller\Back;

use App\Entity\Movie;
use App\Form\MovieType;
use App\Repository\MovieRepository;
use App\Service\Slugger;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Console\Output\BufferedOutput;
use Symfony\Component\Console\Output\NullOutput;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Component\Routing\Annotation\Route;


class MovieController extends AbstractController
{
    /**
     * @Route("back/movie/", name="movie_index", methods={"GET"})
     */
    public function index(MovieRepository $movieRepository): Response
    {
        return $this->render('back/movie/index.html.twig', [
            'movies' => $movieRepository->findAll(),
        ]);
    }

    /**
     * @Route("back/movie/new", name="movie_new", methods={"GET","POST"})
     */
    public function new(Request $request, Slugger $slugger, KernelInterface $kernel): Response
    {
        $movie = new Movie();
        $form = $this->createForm(MovieType::class, $movie);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) 
        {
            // on génère le slug du film à l'aide de la classe qu'on a crée            
            
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($movie);
            $entityManager->flush();
            $slugger->slugifyMovie($movie);

            // on peut lancer les commandes permettant de récupérer les infos des films
            $application = new Application($kernel);
            $application->setAutoExit(false);

            $plotInput = new ArrayInput([
                'command' => 'app:movie:plot-upload',
                'movieId' => $movie->getId()
            ]);
            $posterInput = new ArrayInput([
                'command' => 'app:movie:poster-upload',
                'movieId' => $movie->getId()
            ]);
            $castingInput = new ArrayInput([
                'command' => 'app:movie:casting-upload',
                'movieId' => $movie->getId()
            ]);

            $output = new BufferedOutput();
            $application->run($plotInput, $output);
            $application->run($posterInput, $output);
            $application->run($castingInput, $output);

            $this->addFlash('success', 'ajout effectué ' .PHP_EOL.$output->fetch());

            return $this->redirectToRoute('movie_index');
        }

        return $this->render('back/movie/new.html.twig', [
            'movie' => $movie,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/movie/{id}", name="movie_show", requirements={"id"="\d+"}, methods={"GET"})
     */
    public function show(Movie $movie, Slugger $slugger): Response
    {
        // modifier le code de la route movie/{id} pour
        //    - générer le slug du movie
        
        // $slugger = new Slugger();
        $slugger->slugifyMovie($movie);

        //    - flush notre entité pour avoir les modif en BDD
        $this->getDoctrine()->getManager()->flush();

        //    - rediriger vers la route sus-créée !
        return $this->redirectToRoute('movie_show_slug', ['slug' => $movie->getSlug()]);

        // // récupérer une instance de movieRepository
        // $movie = $movieRepo->findOneWithGenre($id);

        // return $this->render('movie/show.html.twig', [
        //     'movie' => $movie,
        // ]);
    }


    /**
     * @Route("/movie/{slug}", name="movie_show_slug", methods={"GET"})
     */
    public function showSlug(Movie $movie): Response
    {
        return $this->render('front/movie/show.html.twig', [
            'movie' => $movie,
        ]);
    }

    /**
     * @Route("back/movie/{id}", name="movie_showback", methods={"GET"})
     */
    public function showBack(Movie $movie): Response
    {
        return $this->render('back/movie/showback.html.twig', [
            'movie' => $movie,
        ]);
    }

    /**
     * @Route("back/movie/{id}/edit", name="movie_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Movie $movie): Response
    {
        $form = $this->createForm(MovieType::class, $movie);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $movie->setUpdatedAt(new \DateTime());
            $this->getDoctrine()->getManager()->flush();
            $this->addFlash('success', 'mise à jour effectuée');
            return $this->redirectToRoute('movie_index');
        }

        return $this->render('back/movie/edit.html.twig', [
            'movie' => $movie,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("back/movie/{id}", name="movie_delete", methods={"DELETE"})
     */
    public function delete(Request $request, Movie $movie): Response
    {
        if ($this->isCsrfTokenValid('delete'.$movie->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($movie);
            $entityManager->flush();
        }
        $this->addFlash('danger', 'suppression effectuée');
        return $this->redirectToRoute('movie_index');
    }
}
