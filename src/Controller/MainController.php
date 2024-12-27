<?php

namespace App\Controller;

use App\Repository\CategoriesRepository;
use App\Service\CartService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class MainController extends AbstractController
{
    #[Route('/', name: 'main')]
    public function index(CategoriesRepository $categoriesRepository): Response
    {
        //pour faire afficher les catégories je fais appel à au categorieRepository et je l'envoie dans la vue.
        // je pouyrrais faire ça: $category = $categoriesRepositories->findBy() mais je le met directement ici dans la vue
        return $this->render('main/index.html.twig', [
            'categories' => $categoriesRepository->findBy([], ['categoryOrder' => 'asc'])
        ]);
    }

    // public function renderNavbar(CartService $cartService)
    // {
    //     return $this->render('_partials/_nav.html.twig', [
    //         'totalQuantity' => $cartService->getTotalQuantity(),
    //     ]);
    // }
}
