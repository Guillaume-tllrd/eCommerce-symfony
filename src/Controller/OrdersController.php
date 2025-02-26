<?php

namespace App\Controller;

use App\Entity\Orders;
use App\Entity\OrdersDetails;
use App\Repository\ProductsRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/commandes', name: 'app_orders_')]
class OrdersController extends AbstractController
{
    #[Route('/ajout', name: 'add')]
    public function add(SessionInterface $session, ProductsRepository $productsRepository, EntityManagerInterface $em): Response
    {
        // on vérifie que le compte est connecté:
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        // on récupère la panier depuis la session:
        $panier = $session->get('panier', []);

        if ($panier === []) {
            $this->addFlash('message', 'Votre panier est vide');
            return $this->redirectToRoute('main');
        }

        // le panier n'est pas vide, on crée la commande:
        $order = new Orders();

        // on remplit la commande
        $order->setUsers($this->getUser());
        $order->setReference(uniqid());

        // on parcourt le panier que l'on récupère depuis la session pour créer les détails de commande
        foreach ($panier as $item => $quantity) {
            $orderDetails = new OrdersDetails();

            // on va chercher le produit avec la méthode find de productRepository:
            $product = $productsRepository->find($item);
            // dd($product);

            $price = $product->getPrice();

            // on crée les details de commande avec les setters
            $orderDetails->setProducts($product);
            $orderDetails->setPrice($price);
            $orderDetails->setQuantity($quantity);

            // on ajoute les details dans l'entité order
            $order->addOrdersDetail($orderDetails);
        }
        // on execute les requetes en persistant et flushant en dehors de la boucle
        // En revanche il faut rajouter dans l'entity Order et la colone orderDetail le paramètre cascade: ['persist']
        $em->persist($order);
        $em->flush();

        // on n'oublie pas de supprimer le panier:
        $session->remove("panier");

        $this->addFlash('success', 'Commande créée avec succès');
        return $this->redirectToRoute('main');
    }
}
