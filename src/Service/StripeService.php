<?php 

namespace App\Service;

use App\Entity\Course;
use App\Entity\Lesson;
use Doctrine\ORM\EntityManagerInterface; // Ajoutez cette ligne
use Stripe\Checkout\Session as CheckoutSession;
use Stripe\Price;
use Stripe\Product;
use Stripe\StripeClient;

class StripeService
{
    private StripeClient $stripe;
    private EntityManagerInterface $entityManager; // Ajoutez cette ligne

    public function __construct(string $stripeApiSecret, EntityManagerInterface $entityManager) // Modifiez le constructeur
    {
        $this->stripe = new StripeClient($stripeApiSecret);
        $this->entityManager = $entityManager; // Initialisez l'EntityManager
    }

    public function createProduct(Course $course): Product
    {
        return $this->stripe->products->create([
            'name' => $course->getTitle(),
            'description' => $course->getDescription(),
            'active' => true,
        ]);
    }

    public function createPrice(Course $course): Price
    {
        return $this->stripe->prices->create([
            'unit_amount' => (int)($course->getPrice() * 100),  // Montant en centimes
            'currency' => 'eur',
            'product' => $course->getStripeProductId(),
        ]);
    }

    public function updateProduct(Course $course): Product
    {
        return $this->stripe->products->update($course->getStripeProductId(), [
            'name' => $course->getTitle(),
            'description' => $course->getDescription(),
            'active' => true,
        ]);
    }

    // Ajoutez des méthodes pour les leçons
    public function createLessonProduct(Lesson $lesson): Product
    {
        return $this->stripe->products->create([
            'name' => $lesson->getTitle(),
            'description' => $lesson->getContent(),
            'active' => true,
        ]);
    }

    public function createLessonPrice(Lesson $lesson): Price
    {
        return $this->stripe->prices->create([
            'unit_amount' => (int)($lesson->getPrice() * 100),  // Montant en centimes
            'currency' => 'eur',
            'product' => $lesson->getStripeProductId(),
        ]);
    }

    public function updateLessonProduct(Lesson $lesson): Product
    {
        return $this->stripe->products->update($lesson->getStripeProductId(), [
            'name' => $lesson->getTitle(),
            'description' => $lesson->getContent(),
            'active' => true,
        ]);
    }

    public function createCheckoutSession(array $lineItems, string $successUrl, string $cancelUrl): CheckoutSession
    {
        return $this->stripe->checkout->sessions->create([
            'payment_method_types' => ['card'],
            'line_items' => $lineItems,
            'mode' => 'payment',
            'success_url' => $successUrl,
            'cancel_url' => $cancelUrl,
        ]);
    }

    public function createLineItemsFromCart(array $cart): array
    {
        $lineItems = [];
        foreach ($cart as $type => $items) {
            foreach ($items as $id => $item) {
                if ($type === 'lessons') {
                    $lesson = $this->entityManager->getRepository(Lesson::class)->find($id);
                    if ($lesson) {
                        $lineItems[] = [
                            'price_data' => [
                                'currency' => 'eur',
                                'product_data' => [
                                    'name' => $lesson->getTitle(),
                                ],
                                'unit_amount' => (int)($lesson->getPrice() * 100), // Montant en centimes
                            ],
                            'quantity' => $item['quantity'] ?? 1,
                        ];
                    }
                } elseif ($type === 'courses') {
                    $course = $this->entityManager->getRepository(Course::class)->find($id);
                    if ($course) {
                        $lineItems[] = [
                            'price_data' => [
                                'currency' => 'eur',
                                'product_data' => [
                                    'name' => $course->getTitle(),
                                ],
                                'unit_amount' => (int)($course->getPrice() ), // Montant en centimes
                            ],
                            'quantity' => $item['quantity'] ?? 1,
                        ];
                    }
                }
            }
        }
        return $lineItems;
    }
}
