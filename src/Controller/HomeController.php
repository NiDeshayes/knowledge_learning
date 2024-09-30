<?php 

// src/Controller/HomeController.php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class HomeController extends AbstractController
{
    #[Route('/home', name: 'app_home_index')]
    public function index(): Response
    {
        // Vous pouvez récupérer les produits ou d'autres données ici.
        $featuredProducts = [
            // Exemple de produits en vedette (vous pouvez les récupérer depuis la base de données)
            ['name' => 'Cours de Symfony', 'description' => 'Apprenez Symfony de A à Z.', 'price' => '49€'],
            ['name' => 'Leçon de PHP', 'description' => 'Masterisez PHP avec des exercices pratiques.', 'price' => '39€'],
            ['name' => 'JavaScript Avancé', 'description' => 'Approfondissez vos connaissances en JavaScript.', 'price' => '59€'],
        ];

        return $this->render('home/index.html.twig', [
            'controller_name' => 'HomeController',
            'featuredProducts' => $featuredProducts,
            'companyName' => 'Stubborn',
            'companyAddress' => 'Piccadilly Circus, London W1J 0DA, Royaume-Uni',
            'contactEmail' => 'stubborn@blabla.com',
            'slogan' => "Don't compromise on your look",
        ]);
    }
}
