<?php

namespace App\DataFixtures;

use App\Entity\Lesson;
use App\Entity\Course;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {
        // Création des leçons
        $lessonsData = [
            [
                'title' => 'Découverte de l’instrument',
                'slug' => 'decouverte-instrument',
                'duration' => 220,
                'price' => 2600.00,
                'image' => 'accords2.jpg',
                'stripe_price_id' => 'price_1Q5rPAA7w8jlfxqS8sDVHmCw',
                'product_id' => 'prod_QxmzQeYE36X7uw',
            ],
            [
                'title' => 'Les accords et les gammes',
                'slug' => 'accords-gammes',
                'duration' => 120,
                'price' => 2600.00,
                'image' => 'accords2.jpg',
                'stripe_price_id' => 'price_1Q5rQXA7w8jlfxqSeIFOrJCm',
                'product_id' => 'prod_Qxn0OUsXz95Wg1',
            ],
            [
                'title' => 'Apprendre les bases de la musique',
                'slug' => 'bases-musique',
                'duration' => 180,
                'price' => 3000.00,
                'image' => 'bases_musique.jpg',
                'stripe_price_id' => 'price_1Q5rQuA7w8jlfxqSlTzfhACx',
                'product_id' => 'prod_Qxn0qmM6OpAMTO',
            ],
            [
                'title' => 'Techniques de jeu avancées',
                'slug' => 'techniques-avancees',
                'duration' => 240,
                'price' => 3500.00,
                'image' => 'techniques_avancees.jpg',
                'stripe_price_id' => 'price_1Q5rRJA7w8jlfxqS9JD2c4o0',
                'product_id' => 'prod_Qxn1kuIUL7JjEm',
            ],
            [
                'title' => 'Les langages Html et CSS',
                'slug' => 'html-css',
                'duration' => 200,
                'price' => 3200.00,
                'image' => 'html.jpg',
                'stripe_price_id' => 'price_1Q5rS8A7w8jlfxqSdQ2M74sT',
                'product_id' => 'prod_Qxn2mFfxkGui7T',
            ],
            [
                'title' => 'Introduction à JavaScript',
                'slug' => 'introduction-javascript',
                'duration' => 150,
                'price' => 2900.00,
                'image' => 'javascript.jpg',
                'stripe_price_id' => 'price_1Q5rT8A7w8jlfxqS9JD2c4o1',
                'product_id' => 'prod_Qxn3nDg4wJ6g1K',
            ],
        ];

        // // Création et persistance des leçons
        // foreach ($lessonsData as $lessonData) {
        //     $lesson = new Lesson();
        //     $lesson->setTitle($lessonData['title'])
        //            ->setSlug($lessonData['slug'])
        //            ->setDuration($lessonData['duration'])
        //            ->setPrice($lessonData['price'])
        //            ->setImage($lessonData['image'])
        //            ->setStripePriceId($lessonData['stripe_price_id'])
        //            ->setProductId($lessonData['product_id']);

        //     $manager->persist($lesson);
        // }

        // Création des cours (vous pouvez ajuster cela en fonction de vos besoins)
        $coursesData = [
            [
                'title' => 'Cours de musique pour débutants',
                'description' => 'Un cours complet pour apprendre la musique.',
                'lessons' => ['Découverte de l’instrument', 'Les accords et les gammes'],
            ],
            [
                'title' => 'Développement Web pour débutants',
                'description' => 'Apprenez à créer votre propre site web.',
                'lessons' => ['Les langages Html et CSS', 'Introduction à JavaScript'],
            ],
        ];

        // Création et persistance des cours
        foreach ($coursesData as $courseData) {
            $course = new Course();
            $course->setTitle($courseData['title'])
                   ->setDescription($courseData['description']);

            // Association des leçons aux cours
            foreach ($courseData['lessons'] as $lessonTitle) {
                // Vous pouvez récupérer les leçons par titre ici
                $lesson = $manager->getRepository(Lesson::class)->findOneBy(['title' => $lessonTitle]);
                if ($lesson) {
                    $course->addLesson($lesson); // Assurez-vous que la méthode addLesson() est définie dans votre entité Course
                }
            }

            $manager->persist($course);
        }

        // Enregistrement des données en base
        $manager->flush();
    }
}
