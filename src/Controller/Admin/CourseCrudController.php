<?php 

// src/Controller/Admin/CourseCrudController.php

namespace App\Controller\Admin;

use App\Entity\Course;
use Doctrine\ORM\EntityManagerInterface;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\Field;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextareaField;
use EasyCorp\Bundle\EasyAdminBundle\Field\MoneyField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ImageField;
use App\Service\StripeService; // Assurez-vous que le service Stripe est importé

class CourseCrudController extends AbstractCrudController
{
    private StripeService $stripeService;

    public function __construct(StripeService $stripeService)
    {
        $this->stripeService = $stripeService;
    }

    public static function getEntityFqcn(): string
    {
        return Course::class;
    }

    public function configureFields(string $pageName): iterable
    {
        yield TextField::new('title', 'Titre');
        yield TextareaField::new('description', 'Description')->setRequired(false);
        yield MoneyField::new('price', 'Prix')->setCurrency('EUR')->setRequired(true);
        yield TextField::new('theme', 'Thème')->setRequired(false);

        // Utilisez ImageField pour le champ image
        yield ImageField::new('image', 'Image')
            ->setBasePath('images')
            ->setUploadDir('public/images')
            ->setRequired(false)
            ->setHelp('Téléchargez une image pour le cours.');

        // Ajout des champs pour les identifiants Stripe
        yield TextField::new('stripeProductId', 'ID Produit Stripe')->setRequired(false);
        yield TextField::new('stripePriceId', 'ID Prix Stripe')->setRequired(false);
    }

    // Méthode pour persister une nouvelle entité avec Stripe
    public function persistEntity(EntityManagerInterface $entityManager, $entityInstance): void
    {
        /** @var Course $course */
        $course = $entityInstance;

        // Créer le produit Stripe
        $stripeProduct = $this->stripeService->createProduct($course);
        $course->setStripeProductId($stripeProduct->id);

        // Créer le prix Stripe
        $stripePrice = $this->stripeService->createPrice($course);
        $course->setStripePriceId($stripePrice->id);

        // Persister l'entité dans la base de données
        parent::persistEntity($entityManager, $entityInstance);
    }

    // Méthode pour mettre à jour une entité avec Stripe
    public function updateEntity(EntityManagerInterface $entityManager, $entityInstance): void
    {
        /** @var Course $course */
        $course = $entityInstance;

        // Mettre à jour le produit sur Stripe
        $this->stripeService->updateProduct($course);

        // Mettre à jour l'entité dans la base de données
        parent::updateEntity($entityManager, $entityInstance);
    }
}
