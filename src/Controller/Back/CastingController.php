<?php

namespace App\Controller\Back;

use App\Entity\Casting;
use App\Form\CastingType;
use App\Repository\CastingRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/back/casting")
 */
class CastingController extends AbstractController
{
    /**
     * @Route("/", name="casting_index", methods={"GET"})
     */
    public function index(CastingRepository $castingRepository): Response
    {
        return $this->render('back/casting/index.html.twig', [
            'castings' => $castingRepository->findAll(),
        ]);
    }

    /**
     * @Route("/new", name="casting_new", methods={"GET","POST"})
     */
    public function new(Request $request): Response
    {
        $casting = new Casting();
        $form = $this->createForm(CastingType::class, $casting);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($casting);
            $entityManager->flush();
            $this->addFlash('success', 'ajout effectué');
            return $this->redirectToRoute('casting_index');
        }

        return $this->render('back/casting/new.html.twig', [
            'casting' => $casting,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="casting_show", methods={"GET"})
     */
    public function show(Casting $casting): Response
    {
        return $this->render('back/casting/show.html.twig', [
            'casting' => $casting,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="casting_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Casting $casting): Response
    {
        $form = $this->createForm(CastingType::class, $casting);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();
            $this->addFlash('success', 'mise à jour effectuée');

            return $this->redirectToRoute('casting_index');
        }

        return $this->render('back/casting/edit.html.twig', [
            'casting' => $casting,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="casting_delete", methods={"DELETE"})
     */
    public function delete(Request $request, Casting $casting): Response
    {
        if ($this->isCsrfTokenValid('delete'.$casting->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($casting);
            $entityManager->flush();
        }
        $this->addFlash('danger', 'suppression effectuée');
        return $this->redirectToRoute('casting_index');
    }
}
