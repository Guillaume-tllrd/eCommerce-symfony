<?php

namespace App\Controller\Admin;

use App\Entity\Products;
use App\Form\ProductsFormType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\String\Slugger\SluggerInterface;

#[Route('/admin/products', name: 'admin_products_')]
class ProductsController extends AbstractController
{
    #[Route('/', name: 'index')]
    public function index(): Response
    {
        return $this->render('admin/products/index.html.twig');
    }
    #[Route('/ajout', name: 'add')]
    public function add(Request $request, EntityManagerInterface $em, SluggerInterface $slugger): Response
    // j'appelle request car je dois récupérer la requete du form, em pour envoyer en bdd et slugger pour créer un slug
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        // on crée un nouveau produit:
        $product = new Products();

        // on crée le formulaire:
        $productForm = $this->createForm(ProductsFormType::class, $product);

        // on traite la requête du form:
        $productForm->handleRequest($request);

        // on vérifie si le form est soumis et valid
        if ($productForm->isSubmitted() && $productForm->isValid()) {
            // on génère le slug:
            $slug = $slugger->slug($productForm->getName());
            // on le stock dans produit:
            $product->setSlug($slug);
            // on arrondit le prix pour la bdd:
            $prix = $product->getPrice() * 100;
            $product->setPrice($prix);

            // on stocke en bdd:
            $em->persist($product);
            $em->flush();

            $this->addFlash('success', 'Produit ajouté avec succès');
            return $this->redirectToRoute('admin_products_index');
        }

        return $this->render('admin/products/add.html.twig', [
            'productForm' => $productForm->createView()
        ]);
    }
    #[Route('/edition/{id}', name: 'edit')]
    public function edit(Products $product, Request $request, EntityManagerInterface $em, SluggerInterface $slugger): Response
    {
        // on vérifie si l'utilisateur peut éditer avec le voter, on y envoie l'attribut et un obket product comme dans la function supports dans ProductsVoter
        $this->denyAccessUnlessGranted('PRODUCT_EDIT', $product);
        // Avant le formulaire on divise le prix par 100:
        // on arrondit le prix pour la bdd:
        $prix = $product->getPrice() / 100;
        $product->setPrice($prix);

        // on crée le formulaire:
        $productForm = $this->createForm(ProductsFormType::class, $product);

        // on traite la requête du form:
        $productForm->handleRequest($request);

        // on vérifie si le form est soumis et valid
        if ($productForm->isSubmitted() && $productForm->isValid()) {
            // on génère le slug:
            $slug = $slugger->slug($productForm->getName());
            // on le stock dans produit:
            $product->setSlug($slug);
            // on arrondit le prix pour la bdd:
            $prix = $product->getPrice() * 100;
            $product->setPrice($prix);

            // on stocke en bdd:
            $em->persist($product);
            $em->flush();

            $this->addFlash('success', 'Produit modifié avec succès');
            return $this->redirectToRoute('admin_products_index');
        }

        return $this->render('admin/products/edit.html.twig', [
            'productForm' => $productForm->createView()
        ]);
    }
    #[Route('/suppression/{id}', name: 'delete')]
    public function delete(Products $product): Response
    {
        return $this->render('admin/products/index.html.twig');
    }
}
