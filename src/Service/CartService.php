<?php

namespace App\Service;

use Symfony\Component\HttpFoundation\Session\SessionInterface;

class CartService
{
    private $session;

    public function __construct(SessionInterface $session)
    {
        $this->session = $session;
    }

    public function getTotalQuantity(): int
    {
        $panier = $this->session->get('panier', []);
        $totalQuantity = 0;

        foreach ($panier as $quantity) {
            $totalQuantity += $quantity;
        }

        return $totalQuantity;
    }
}
