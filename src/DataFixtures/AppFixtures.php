<?php

namespace App\DataFixtures;

use App\Entity\Client;
use App\Entity\Commande;
use App\Entity\Produit;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create('fr_FR');   // données en français

        // 1. Les sneakers : [marque, nom, catégorie, prix, stock]
        $catalogue = [
            ['Nike', 'Air Max 90', 'Lifestyle', '149.99', 25],
            ['Nike', 'Air Force 1 \'07', 'Lifestyle', '119.99', 40],
            ['Nike', 'Pegasus 41', 'Running', '139.99', 18],
            ['Adidas', 'Samba OG', 'Lifestyle', '119.95', 30],
            ['Adidas', 'Ultraboost 5', 'Running', '189.95', 12],
            ['Adidas', 'Forum Low', 'Basket', '109.95', 0],
            ['New Balance', '550', 'Basket', '129.99', 22],
            ['New Balance', '1906R', 'Running', '159.99', 9],
            ['Asics', 'Gel-Kayano 14', 'Running', '159.90', 14],
            ['Puma', 'Suede Classic', 'Lifestyle', '84.95', 35],
            ['Vans', 'Old Skool', 'Skate', '79.99', 50],
            ['Converse', 'Chuck 70 High', 'Lifestyle', '94.99', 0],
        ];

        $produits = [];
        foreach ($catalogue as [$marque, $nom, $categorie, $prix, $stock]) {
            $produit = new Produit();
            $produit->setMarque($marque);
            $produit->setNom($nom);
            $produit->setCategorie($categorie);
            $produit->setPrix($prix);          // DECIMAL = chaîne en PHP
            $produit->setStock($stock);
            $produit->setImage('https://placehold.co/600x400?text=' . urlencode($marque . ' ' . $nom));

            $manager->persist($produit);
            $produits[] = $produit;            // on les garde pour les commandes
        }

        // 2. Les clients, générés par Faker
        $clients = [];
        for ($i = 0; $i < 15; $i++) {
            $client = new Client();
            $client->setNom($faker->name());
            $client->setVille($faker->city());
            $client->setEmail($faker->unique()->safeEmail());

            $manager->persist($client);
            $clients[] = $client;
        }

        // 3. Les commandes : un client et une sneaker au hasard
        for ($i = 0; $i < 40; $i++) {
            $commande = new Commande();
            $commande->setClient($faker->randomElement($clients));
            $commande->setProduit($faker->randomElement($produits));
            $commande->setQuantite($faker->numberBetween(1, 3));
            $commande->setDateCommande(
                \DateTimeImmutable::createFromMutable($faker->dateTimeBetween('-6 months'))
            );

            $manager->persist($commande);
        }

        // 4. Un seul flush : tout part en base d'un coup
        $manager->flush();
    }
}