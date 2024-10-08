<?php // src/Controller/LessonsController.php

namespace App\Controller;

use App\Entity\Lesson;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class LessonsController extends AbstractController
{
    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager; 
    }

    #[Route('/lessons', name: 'app_lessons')]
    public function index(): Response
    {
        // Récupérer toutes les leçons
        $lessonRepository = $this->entityManager->getRepository(Lesson::class);
        $lessons = $lessonRepository->findAll();

        // Vérifiez le rôle de l'utilisateur
        if ($this->isGranted('ROLE_ADMIN')) {
            return $this->render('lessons/admin.html.twig', [
                'lessons' => $lessons,
            ]);
        }

        if ($this->isGranted('ROLE_CURSUS D’INITIATION à LA GUITARE')) {
            return $this->render('lessons/Guitare.html.twig', [
                'lessons' => $lessons, // Vous pouvez filtrer les leçons si nécessaire
            ]);
        }

        if ($this->isGranted('ROLE_CURSUS D’INITIATION AU PIANO')) {
            return $this->render('lessons/Piano.html.twig', [
                'lessons' => $lessons, // Idem pour ce rôle
            ]);
        }

        if ($this->isGranted('ROLE_CURSUS D’INITIATION AU DéVELOPPEMENT WEB')) {
            return $this->render('lessons/Htttp.html.twig', [
                'lessons' => $lessons,
            ]);
        }

        if ($this->isGranted('ROLE_O CURSUS D’INITIATION à L’ART DU DRESSAGE CULINAIRE')) {
            return $this->render('lessons/Dressage.html.twig', [
                'lessons' => $lessons,
            ]);
        }

        if ($this->isGranted('ROLE_CURSUS D’INITIATION à LA CUISINE')) {
            return $this->render('lessons/Cuisine.html.twig', [
                'lessons' => $lessons,
            ]);
        }

        if ($this->isGranted('ROLE_CURSUS D’INITIATION AU JARDINAGE')) {
            return $this->render('lessons/Jardinage.html.twig', [
                'lessons' => $lessons,
            ]);
        }

        // Si l'utilisateur n'a aucun rôle correspondant, redirigez vers une page par défaut
        return $this->redirectToRoute('app_default');
    }

    #[Route('/lessons/lesson1', name: 'app_lesson1')]
    public function lesson1(): Response
    {
        return $this->render('lessons/Découverte_de_linstrument.html.twig');
    }

    #[Route('/lessons/lesson2', name: 'app_lesson2')]
    public function lesson2(): Response
    {
        return $this->render('lessons/Cursus_de_guitare.html.twig');
    }

    #[Route('/lessons/lesson3', name: 'app_lesson3')]
    public function lesson3(): Response
    {
        return $this->render('lessons/Accords_Piano.html.twig');
    }

    #[Route('/lessons/lesson4', name: 'app_lesson4')]
    public function lesson4(): Response
    {
        return $this->render('lessons/Cursus_Piano.html.twig');
    }

    #[Route('/lessons/lesson5', name: 'app_lesson5')]
    public function lesson5(): Response
    {
        return $this->render('lessons/Http.html.twig');
    }

    #[Route('/lessons/lesson6', name: 'app_lesson6')]
    public function lesson6(): Response
    {
        return $this->render('lessons/Css.html.twig');
    }
    #[Route('/lessons/lesson7', name: 'app_lesson7')]
    public function lesson7(): Response
    {
        return $this->render('lessons/Jardinage_outil.html.twig');
    }

    #[Route('/lessons/lesson8', name: 'app_lesson8')]
    public function lesson8(): Response
    {
        return $this->render('lessons/Jardinage_lune.html.twig');
    }
    #[Route('/lessons/lesson9', name: 'app_lesson9')]
    public function lesson9(): Response
    {
        return $this->render('lessons/Cuisson.html.twig');
    }

    #[Route('/lessons/lesson10', name: 'app_lesson10')]
    public function lesson10(): Response
    {
        return $this->render('lessons/Cuisine_saveur.html.twig');
    }
    #[Route('/lessons/lesson11', name: 'app_lesson11')]
    public function lesson11(): Response
    {
        return $this->render('lessons/Style_assiete.html.twig');
    }

    #[Route('/lessons/lesson12', name: 'app_lesson12')]
    public function lesson12(): Response
    {
        return $this->render('lessons/Harmoniser.html.twig');
    }
}
