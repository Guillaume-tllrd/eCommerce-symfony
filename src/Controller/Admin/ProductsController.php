<?php

namespace App\Controller\Admin;

use App\Entity\Images;
use App\Entity\Products;
use App\Form\ProductsFormType;
use App\Service\PictureService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
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
    public function add(Request $request, EntityManagerInterface $em, SluggerInterface $slugger, PictureService $pictureService): Response
    // j'appelle request car je dois récupérer la requete du form, em pour envoyer en bdd et slugger pour créer un slug
    {

        // on crée un nouveau produit:
        $product = new Products();

        // on crée le formulaire:
        $productForm = $this->createForm(ProductsFormType::class, $product);

        // on traite la requête du form:
        $productForm->handleRequest($request);

        // on vérifie si le form est soumis et valid
        if ($productForm->isSubmitted() && $productForm->isValid()) {
            // on récupère les images:
            $images = $productForm->get('images')->getData();

            foreach ($images as $image) {
                // on définit le dossier de destination:
                $folder = 'products';

                // on appelle le service d'ajout PictureService
                $fichier = $pictureService->add($image, $folder, 300, 300);

                $img = new Images();
                $img->setName($fichier);
                $product->addImage($img);
            }

            // on génère le slug:
            $slug = $slugger->slug($productForm->getName());
            // on le stock dans produit:
            $product->setSlug($slug);
            // PLUS BESOIN D'ARRONDIR avec MoneyType dans le form 
            // on arrondit le prix pour la bdd:
            // $prix = $product->getPrice() * 100;
            // $product->setPrice($prix);

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
    public function edit(Products $product, Request $request, EntityManagerInterface $em, SluggerInterface $slugger, PictureService $pictureService): Response
    {
        // on vérifie si l'utilisateur peut éditer avec le voter, on y envoie l'attribut et un obket product comme dans la function supports dans ProductsVoter
        $this->denyAccessUnlessGranted('PRODUCT_EDIT', $product);
        // Avant le formulaire on divise le prix par 100:
        // on arrondit le prix pour la bdd:
        // $prix = $product->getPrice() / 100;
        // $product->setPrice($prix);

        // on crée le formulaire:
        $productForm = $this->createForm(ProductsFormType::class, $product);

        // on traite la requête du form:
        $productForm->handleRequest($request);

        // on vérifie si le form est soumis et valid
        if ($productForm->isSubmitted() && $productForm->isValid()) {

            // on récupère les images:
            $images = $productForm->get('images')->getData();

            foreach ($images as $image) {
                // on définit le dossier de destination:
                $folder = 'products';

                // on appelle le service d'ajout PictureService
                $fichier = $pictureService->add($image, $folder, 300, 300);

                $img = new Images();
                $img->setName($fichier);
                $product->addImage($img);
            }
            // on génère le slug:
            $slug = $slugger->slug($productForm->getName());
            // on le stock dans produit:
            $product->setSlug($slug);

            // PLUS BESOIN D'ARRONDIR avec MoneyType dans le form 
            // on arrondit le prix pour la bdd:
            // $prix = $product->getPrice() * 100;
            // $product->setPrice($prix);

            // on stocke en bdd:
            $em->persist($product);
            $em->flush();

            $this->addFlash('success', 'Produit modifié avec succès');
            return $this->redirectToRoute('admin_products_index');
        }

        return $this->render('admin/products/edit.html.twig', [
            'productForm' => $productForm->createView(),
            'product' => $product
        ]);
    }
    #[Route('/suppression/{id}', name: 'delete')]
    public function delete(Products $product): Response
    {
        $this->denyAccessUnlessGranted('PRODUCT_DELETE', $product);
        return $this->render('admin/products/index.html.twig');
    }

    #[Route('/suppression/image/{id}', name: 'delete_image', methods: ['DELETE'])]
    public function deleteImage(Images $image, Request $request, EntityManagerInterface $em, PictureService $pictureService): JsonResponse
    {
        // On récupère le contenu de la requête 
        $data = json_decode($request->getContent(), true);
        // on récupère e csrfToken qui s'appelle delete dans data-token depuis le a dans le form:
        if ($this->isCsrfTokenValid('delete' . $image->getId(), $data['_token'])) {
            // le token csrf est valide
            // on récupère le nom de l'image:
            $nom = $image->getName();

            if ($pictureService->delete($nom, 'products', 300, 300)) {
                // on envoie à la bdd
                $em->remove($image);
                $em->flush();

                return new JsonResponse(["success" => true], 200);
            }
            // si on rntre dans pas dans le if , la suppreission a échoué: 
            return new JsonResponse(["error" => 'Erreur de suppression'], 400);
        }
        return new JsonResponse(["error" => 'Token invalide'], 400);
    }
}
