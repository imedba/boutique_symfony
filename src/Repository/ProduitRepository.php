<?php

namespace App\Repository;

use App\Entity\Produit;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Produit>
 */
class ProduitRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Produit::class);
    }

    /** Catalogue filtré par catégorie et/ou marque, trié */
    public function findCatalogue(?string $categorie, ?string $marque, ?string $tri): array
    {
        $qb = $this->createQueryBuilder('p');

        if ($categorie) {
            $qb->andWhere('p.categorie = :categorie')->setParameter('categorie', $categorie);
        }

        if ($marque) {
            $qb->andWhere('p.marque = :marque')->setParameter('marque', $marque);
        }

        match ($tri) {
            'prix_asc'  => $qb->orderBy('p.prix', 'ASC'),
            'prix_desc' => $qb->orderBy('p.prix', 'DESC'),
            default     => $qb->orderBy('p.marque')->addOrderBy('p.nom'),
        };

        return $qb->getQuery()->getResult();
    }

    public function findProduits(): array
    {
        // Ci dessous je construit la requete SQL pour récupérer tout les produits
        $lignes = $this->createQueryBuilder('p')
        // Le temps de ma requete, p = la table produits
        ->select('p')
        // L'ordre de classement se fera dans l'ordre alphabetique des noms des produits
        ->orderBy('p.nom')
        // Définis la requete
        ->getQuery()
        // Execute et envoie moi le resultat de la requete
        ->getResult();
        return array_column($lignes, 'produits');

    }

    /** Liste des marques, sans doublon */
    public function findMarques(): array
    {
        $lignes = $this->createQueryBuilder('p')
            ->select('DISTINCT p.marque')
            ->orderBy('p.marque')
            ->getQuery()
            ->getResult();

        return array_column($lignes, 'marque');
    }

    /** Liste des catégories, sans doublon */
    public function findCategories(): array
    {
        $lignes = $this->createQueryBuilder('p')
            ->select('DISTINCT p.categorie')
            ->where('p.categorie IS NOT NULL')
            ->orderBy('p.categorie')
            ->getQuery()
            ->getResult();

        return array_column($lignes, 'categorie');
    }

    /** Les dernières sneakers ajoutées */
    public function findNouveautes(int $nombre = 4): array
    {
        return $this->findBy([], ['id' => 'DESC'], $nombre);
    }

    /** Les sneakers les plus vendues (somme des quantités commandées) */
    public function findMeilleuresVentes(int $nombre = 4): array
    {
        return $this->createQueryBuilder('p')
            ->join('p.commandes', 'c')
            ->addSelect('SUM(c.quantite) AS HIDDEN totalVendu')
            ->groupBy('p.id')
            ->orderBy('totalVendu', 'DESC')
            ->setMaxResults($nombre)
            ->getQuery()
            ->getResult();
    }

    /** Sneakers de la même catégorie, sauf celle affichée */
    public function findSimilaires(Produit $produit, int $nombre = 4): array
    {
        return $this->createQueryBuilder('p')
            ->where('p.categorie = :categorie')
            ->andWhere('p.id != :id')
            ->setParameter('categorie', $produit->getCategorie())
            ->setParameter('id', $produit->getId())
            ->setMaxResults($nombre)
            ->getQuery()
            ->getResult();
    }

//    /**
//     * @return Produit[] Returns an array of Produit objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('p')
//            ->andWhere('p.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('p.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Produit
//    {
//        return $this->createQueryBuilder('p')
//            ->andWhere('p.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
