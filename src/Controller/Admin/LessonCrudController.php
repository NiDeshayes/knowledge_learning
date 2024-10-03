<?php 

// src/Controller/Admin/LessonCrudController.php

namespace App\Controller\Admin;

use App\Entity\Lesson;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextareaField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IntegerField;
use EasyCorp\Bundle\EasyAdminBundle\Field\MoneyField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ImageField; 
use App\Service\StripeService;
use Doctrine\ORM\EntityManagerInterface; // Assurez-vous d'importer EntityManagerInterface

class LessonCrudController extends AbstractCrudController
{
    private StripeService $stripeService;

    public function __construct(StripeService $stripeService)
    {
        $this->stripeService = $stripeService;
    }

    public static function getEntityFqcn(): string
    {
        return Lesson::class;
    }

    public function configureFields(string $pageName): iterable
    {
        yield TextField::new('title', 'Titre');
        yield TextareaField::new('content', 'Contenu');
        yield TextField::new('videoUrl', 'URL de la vidéo');
        yield IntegerField::new('duration', 'Durée (minutes)');
        yield MoneyField::new('price', 'Prix')->setCurrency('EUR');
        
        // Utilisez ImageField pour le champ image
        yield ImageField::new('image', 'Image')
            ->setBasePath('images/') // Chemin d'accès pour afficher l'image
            ->setUploadDir('public/images/') // Répertoire de téléchargement
            ->setRequired(false)
            ->setHelp('Téléchargez une image pour la leçon.');

        yield AssociationField::new('course', 'Cours')->setRequired(true);

         // Ajout des champs pour les identifiants Stripe
         yield TextField::new('stripeProductId', 'ID Produit Stripe')->setRequired(false);
         yield TextField::new('stripePriceId', 'ID Prix Stripe')->setRequired(false);
    }

     // Méthode pour persister une nouvelle entité avec Stripe
     public function persistEntity(EntityManagerInterface $entityManager, $entityInstance): void
     {
         /** @var Lesson $lesson */
         $lesson = $entityInstance;

         // Créer le produit Stripe
         $stripeProduct = $this->stripeService->createLessonProduct($lesson); // Assurez-vous que createProduct accepte un objet Lesson
         $lesson->setStripeProductId($stripeProduct->id);
 
         // Créer le prix Stripe
         $stripePrice = $this->stripeService->createLessonPrice($lesson); // Assurez-vous que createPrice accepte un objet Lesson
         $lesson->setStripePriceId($stripePrice->id);
 
         // Persister l'entité dans la base de données
         parent::persistEntity($entityManager, $entityInstance);
     }
 
     // Méthode pour mettre à jour une entité avec Stripe
     public function updateEntity(EntityManagerInterface $entityManager, $entityInstance): void
     {
         /** @var Lesson $lesson */
         $lesson = $entityInstance;
 
         // Mettre à jour le produit sur Stripe
         $this->stripeService->updateLessonProduct($lesson); // Assurez-vous que updateProduct accepte un objet Lesson
 
         // Mettre à jour l'entité dans la base de données
         parent::updateEntity($entityManager, $entityInstance);
     }
}
