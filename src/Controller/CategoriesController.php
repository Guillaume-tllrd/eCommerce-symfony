<?php

namespace App\Controller;

use App\Entity\Categories;
use App\Entity\Products;
use App\Repository\CategoriesRepository;
use App\Repository\ProductsRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
// use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;

#[Route('/categories', name: 'categories_')]
class CategoriesController extends AbstractController
{
    #[Route('/', name: 'index')]
    public function index(CategoriesRepository $categoriesRepository): Response
    {
        //pour faire afficher les catégories je fais appel à au categorieRepository et je l'envoie dans la vue.
        // je pouyrrais faire ça: $category = $categoriesRepositories->findBy() mais je le met directement ici dans la vue
        return $this->render('categories/index.html.twig', [
            'categories' => $categoriesRepository->findBy([], ['categoryOrder' => 'asc'])
        ]);
    }
    // pour faire une route dynamique on met entre parenthèse cad on va chercher une variable. cela fonctionne uniquement si le champ utilisé dans l'URL est la clé primaire par défaut (comme l'id)
    #[Route('/{id}', name: 'list')]
    // Obligé de faire avec l'id pour la version 7.2 on ne peut faire avec le slug, il aurait fallaut rajouter ParamConerter acquis avec composer require sensio/framework-extra-bundle
    // #[ParamConverter('category', options: ['mapping' => ['slug' => 'slug']])]
    public function list(Categories $category, ProductsRepository $productsRepository, Request $request): Response
    {
        // On va chercher le numéro de page dans l'url, on la met en défaut à 1:
        $page = $request->query->getInt('page', 1);
        $products = $productsRepository->findProductsPaginated($page, $category->getId(), 2) ?? [
            'data' => [],
            'pages' => 1,
            'page' => $page,
            'limit' => 2
        ];
        // AVANT:
        //on va chercher la liste des produits de la catégorie dans l'entité categories, ouis on le passe dans la vue
        // $products = $category->getProducts();

        return $this->render('categories/list.html.twig', compact('category', 'products'));
        // compact permet d'envoyer un tableau associatif , évite de faire : 
        // return $this->render('categories/list.html.twig', ['category" => $category, "products" => $products]);
    }
}
