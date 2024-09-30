<?php 


// src/Controller/AdminController.php
namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AdminController extends AbstractController
{
    #[Route('/admin', name: 'app_admin_index')]
    public function index(): Response
    {
        // Logique pour l'administration
        return $this->render('admin/index.html.twig');
    }

    // Ajoute d'autres actions pour gérer les utilisateurs, les produits, etc.
}
