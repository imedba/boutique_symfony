<?php

namespace App\Controller;

use App\Repository\ProduitRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class AccueilController extends AbstractController
{
    // Ci dessous, je créer une annotation symfony qui va me permettre de créer une route pour accéder à ma page d'accueil
    #[Route('/accueil', name: 'app_accueil')]
    public function index(ProduitRepository $produitRepository): Response
    {
        $liste_marques = $produitRepository->findMarques();
        // var_dump($liste_marques);
        return $this->render('accueil/index.html.twig', [
            'marques' => $liste_marques,
            'ville' => 'Paris',
        ]);
    }
}
