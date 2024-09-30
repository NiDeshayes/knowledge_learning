<?php

namespace App\Controller\Admin;

use App\Entity\User;
use App\Entity\Course; // N'oublie pas d'importer Course
use App\Entity\Lesson; // N'oublie pas d'importer Lesson
use EasyCorp\Bundle\EasyAdminBundle\Config\Dashboard;
use EasyCorp\Bundle\EasyAdminBundle\Config\MenuItem;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractDashboardController;
use EasyCorp\Bundle\EasyAdminBundle\Router\AdminUrlGenerator;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DashboardController extends AbstractDashboardController
{
    public function __construct(
        private readonly AdminUrlGenerator $adminUrlGenerator
    ) {
    }

    #[Route('/admin', name: 'admin')]
    public function index(): Response
    {
        // Redirection vers la page des utilisateurs à l'ouverture du dashboard
        $url = $this->adminUrlGenerator
            ->setController(UserCrudController::class) // Redirige vers les utilisateurs ou un autre contrôleur de ton choix
            ->generateUrl();

        return $this->redirect($url);
    }

    public function configureDashboard(): Dashboard
    {
        return Dashboard::new()
            ->setTitle('Admin');
    }

    public function configureMenuItems(): iterable
    {
        // Lien vers le Dashboard principal
        yield MenuItem::linkToDashboard('Dashboard', 'fa fa-home');

        // Lien pour gérer les utilisateurs
        yield MenuItem::linkToCrud('Utilisateurs', 'fas fa-user', User::class);

        // Lien pour gérer les cours
        yield MenuItem::linkToCrud('Cours', 'fas fa-book', Course::class);

        // Lien pour gérer les leçons
        yield MenuItem::linkToCrud('Leçons', 'fas fa-chalkboard-teacher', Lesson::class);
    }
}
