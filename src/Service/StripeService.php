<?php 

// src/Service/StripeService.php

namespace App\Service;

use App\Entity\Course;   // Assurez-vous que vous avez cette classe
use App\Entity\Lesson;   // Assurez-vous que vous avez cette classe
use Stripe\Checkout\Session as CheckoutSession;
use Stripe\Price;
use Stripe\StripeClient;

class StripeService
{
    private StripeClient $stripe;

    public function __construct(string $stripeApiSecret)
    {
        $this->stripe = new StripeClient($stripeApiSecret);
    }

    public function createCourse(Course $course): \Stripe\Product
    {
        return $this->stripe->products->create([
            'name' => $course->getTitle(),  // Modifié de getName() à getTitle()
            'description' => $course->getDescription(),
            'active' => true,  // Vous pouvez gérer l'état actif comme vous le souhaitez
        ]);
    }

    public function createLesson(Lesson $lesson): Price
    {
        return $this->stripe->prices->create([
            'unit_amount' => (int)($lesson->getPrice() * 100),  // Assurez-vous que getPrice retourne en euros, converti en centimes
            'currency' => 'eur',
            'product' => $lesson->getCourse()->getStripeProductId(), // Assurez-vous que getCourse() existe et retourne l'ID du produit
        ]);
    }

    public function updateCourse(Course $course): \Stripe\Product
    {
        return $this->stripe->products->update($course->getStripeProductId(), [
            'name' => $course->getTitle(),  // Modifié de getName() à getTitle()
            'description' => $course->getDescription(),
            'active' => true,  // Mettez à jour en fonction de votre logique
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
        
        foreach ($cart['courses'] ?? [] as $item) {
            $lineItems[] = [
                'price' => $item['course']->getStripePriceId(),  // Assurez-vous que cette méthode existe
                'quantity' => $item['quantity'],
            ];
        }

        foreach ($cart['lessons'] ?? [] as $item) {
            $lineItems[] = [
                'price' => $item['lesson']->getStripePriceId(),  // Assurez-vous que cette méthode existe
                'quantity' => $item['quantity'],
            ];
        }

        return $lineItems;
    }
}
