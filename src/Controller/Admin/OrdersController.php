<?php

namespace App\Controller\Admin;

use App\Repository\OrdersRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/admin/orders', name: 'admin_orders_')]
class OrdersController extends AbstractController
{

    #[Route('/', name: 'index')]
    public function index(OrdersRepository $ordersRepository): Response
    {
        $commandes = $ordersRepository->findAll();
        // dd($commandes);
        return $this->render('admin/orders/index.html.twig', compact('commandes'));
    }
}
