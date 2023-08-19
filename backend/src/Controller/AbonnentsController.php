<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class AbonnentsController extends AbstractController
{
    /**
     * @Route("/abonnents", name="abonnents")
     */
    public function index()
    {
        return $this->json([
            'message' => 'Welcome to your new controller!',
            'path' => 'src/Controller/AbonnentsController.php',
        ]);
    }
}
