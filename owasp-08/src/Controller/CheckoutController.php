<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\Order;
use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class CheckoutController extends AbstractController
{
    #[Route('/checkout/{id}', name: 'checkout_show', methods: ['GET'])]
    public function show(Order $order, Security $security): Response
    {
        $currentUser = $security->getUser();

        if (!$currentUser instanceof User || $order->getCustomer() !== $currentUser) {
            throw $this->createAccessDeniedException();
        }

        return $this->render('checkout/show.html.twig', [
            'order' => $order,
        ]);
    }
}
