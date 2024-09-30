<?php

// src/Service/CartService.php

namespace App\Service;

class CartService
{
    private $cart = [];

    public function addToCart($product, $quantity): void
    {
        $this->cart[$product->getId()] = [
            'product' => $product,
            'quantity' => $quantity
        ];
    }

    public function getCartItems(): array
    {
        return $this->cart;
    }

    public function getCartTotal(): float
    {
        $total = 0;
        foreach ($this->cart as $item) {
            $total += $item['product']->getPrice() * $item['quantity'];
        }
        return $total;
    }
}
