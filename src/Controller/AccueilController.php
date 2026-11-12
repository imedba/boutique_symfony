<?php

namespace App\Controller;

use App\Repository\ProduitRepository;
use App\Entity\Produit;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class AccueilController extends AbstractController
{
    // Ci dessous, je créer une annotation symfony qui va me permettre de créer une route pour accéder à ma page d'accueil
    #[Route('/', name: 'app_accueil')]
    public function index(ProduitRepository $produitRepository): Response
    {
        // Ci dessous je récupere la liste de toutes les marques grace à la methode findMarque() qui est dans produitRepository
        $liste_marques = $produitRepository->findMarques();
        $liste_categories = $produitRepository->findCategories();
        $liste_produits =  $produitRepository->findAll();
        // var_dump($liste_produits);
        // var_dump($liste_marques);
        return $this->render('accueil/index.html.twig', [
            'marques' => $liste_marques,
            'categories' => $liste_categories,
            'produits' => $liste_produits
        ]);
    }

    // Grace a cette annotation, je récupère l'id tapé dans l'url
    // Le fait de donner en parametre à la méthode show Produit $produit, fait que
    // $produit sera l'objet Produit qui a pour id => {id}
    #[Route('/produit/{id}', name: 'app_produit')]
    public function show(Produit $produit, ProduitRepository $produitRepository)
    {
        // echo($produit->getNom());
        // echo($produit->getMarque());
        $produits_similaires = $produitRepository->findSimilaires($produit, 4);
        return $this->render('accueil/produit.html.twig', [
            'produit' => $produit,
            'produits_similaire' => $produits_similaires
        ]);
    }
}
