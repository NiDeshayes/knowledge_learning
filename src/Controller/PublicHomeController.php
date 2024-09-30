<?php 

// src/Controller/PublicHomeController.php
namespace App\Controller;

use App\Entity\Product;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class PublicHomeController extends AbstractController
{
    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    #[Route('/', name: 'app_public_home')]
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

    

