<?php

namespace App\Controller;

use App\Entity\Products;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/produits', name: 'products_')]

class ProductsController extends AbstractController
{
    #[Route('/', name: 'index')]
    public function index(): Response
    {
        return $this->render('products/index.html.twig', [
            'controller_name' => 'ProductsController',
        ]);
    }
    // pour faire une route dynamique on met entre parenthèse cad on va chercher une variable
    #[Route('/{id}', name: 'details')]
    public function details(Products $product): Response
    {


        return $this->render('products/details.html.twig', compact('product'));
        // compact permet d'envoyer un tableau associatif , évite de faire : ['product' => $product]
    }
}
