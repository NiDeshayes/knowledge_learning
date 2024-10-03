<?php 

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class CheckoutController extends AbstractController
{
    #[Route('/checkout/success', name: 'checkout_success')]
    public function success(): Response
    {
        return $this->render('checkout/success.html.twig');
    }

    #[Route('/checkout/cancel', name: 'checkout_cancel')]
    public function cancel(): Response
    {
        return $this->render('checkout/cancel.html.twig');
    }
}
