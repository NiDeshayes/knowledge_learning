<?php 

// src/EventListener/AccessDeniedListener.php
namespace App\EventListener;

use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Routing\RouterInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;


class AccessDeniedListener
{
    private $logger;
    private $router;

    public function __construct(LoggerInterface $logger, RouterInterface $router)
    {
        $this->logger = $logger;
        $this->router = $router;
    }

    public function onKernelException(ExceptionEvent $event)
    {
        $exception = $event->getThrowable();

        if ($exception instanceof AccessDeniedException || $exception instanceof AccessDeniedHttpException) {
            $this->logger->warning('Access denied: ' . $exception->getMessage());
            // Rediriger vers la page de connexion
            $response = new RedirectResponse($this->router->generate('app_login'));
            $event->setResponse($response);
        }
    }
}
