<?php

namespace App\Controller\Back;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AdminController extends AbstractController
{

    /**
     * @Route("/back/admin", name="back_admin")
     */
    public function index(): Response
    
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        return $this->render('back/admin/index.html.twig');
    }
}
