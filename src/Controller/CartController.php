<?php

namespace App\Controller;

use App\Entity\Products;
use App\Repository\ProductsRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;

#[Route('cart', name: 'cart_')]
class CartController extends AbstractController
{

    #[Route('/', name: 'index')]
    public function index(SessionInterface $session, ProductsRepository $productsRepository)
    {
        // on crée cette route pour pouvoir accéder à notre panier
        $panier = $session->get('panier', []);

        // on initialise des variables
        $data = [];
        $total = 0;

        // ensuite on boucle sur chaque produit du panier, on va chercher son nom, ses infos et on y met dans le tableau data, dans $panier on récupère l'id du produit c'est pour ça qu'on l'appel id et quantité est sa valeur

        foreach ($panier as $id => $quantity) {
            // je récupère le produit grace au repository et la méthode find
            $product = $productsRepository->find($id);

            $data[] = [
                'product' => $product,
                'quantity' => $quantity
            ];
            $total += $product->getPrice() * $quantity;
        }
        // dd($data);
        return $this->render('cart/index.html.twig', compact('data', "total"));
    }
    #[Route('/add/{id}', name: 'add')]
    public function add(Products $product, SessionInterface $session)
    {
        // On récupère l'id du produit:
        $id = $product->getId();

        // on récupère le panier existant s'il y en a pas tu me mets un tableau vide:
        $panier = $session->get('panier', []);

        // on ajoute le produit dans le panier s'il n'y est pas encore
        // Sinon on incrémente sa quantité:
        if (empty($panier[$id])) {
            $panier[$id] = 1;
        } else {
            $panier[$id]++;
        }

        // pour mettre dans la session,je fais un set, j'indique le nom et j'y met la variable panier:
        $session->set('panier', $panier);

        // on redirige vers la page du panier:
        return $this->redirectToRoute('cart_index');
    }

    #[Route('/remove/{id}', name: 'remove')]
    public function remove(Products $product, SessionInterface $session)
    {
        // On récupère l'id du produit:
        $id = $product->getId();

        // on récupère le panier existant s'il y en a pas tu me mets un tableau vide:
        $panier = $session->get('panier', []);

        // on retire le produit dans le panier s'il n'y a qu'un exemplaire
        // Sinon on décrémente sa quantité:
        if (!empty($panier[$id])) {
            if ($panier[$id] > 1) {
                $panier[$id]--;
            }
        } else {
            // sinon on enlève la var avec unset
            unset($panier[$id]);
        }

        // pour mettre dans la session,je fais un set, j'indique le nom et j'y met la variable panier:
        $session->set('panier', $panier);

        // on redirige vers la page du panier:
        return $this->redirectToRoute('cart_index');
    }

    #[Route('/delete/{id}', name: 'delete')]
    public function delete(Products $product, SessionInterface $session)
    {
        $id = $product->getId();

        $panier = $session->get('panier', []);

        // pour supprimer si le panier n'est pas vide on fait le unset directement.
        if (!empty($panier[$id])) {

            unset($panier[$id]);
        }

        $session->set('panier', $panier);

        return $this->redirectToRoute('cart_index');
    }

    #[Route('/empty', name: 'empty')]
    public function empty(SessionInterface $session)
    {
        // pour le btn vider le panier
        // pas besoin d'utiliser d'id et de product on supprime directement la session.
        $session->remove('panier');

        return $this->redirectToRoute('cart_index');
    }
}
