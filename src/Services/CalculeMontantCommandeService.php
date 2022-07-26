<?php

namespace App\Services;

use App\Entity\Commande;
use DateTime;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;


final class CalculeMontantCommandeService{

    private ?TokenInterface $token;

    public function __construct( TokenStorageInterface $tokenStorage) {

        $this->token = $tokenStorage->getToken();

    }

    public function calcule(Commande $commande){
        $montant = 0;
        
        // foreach ($commande->getProduitCommandes() as $prod_com) {
        //     $montant = $montant + ($prod_com->getQuantiteProduit() * $prod_com->getPrix());
        // }

        if($commande->getQuartier() != null){
           $montant = $montant + $commande->getQuartier()->getZone()->getPrix();
        }

        $commande->setMontant($montant);
        $commande->setDate(new DateTime());
        $commande->setClient($this->token->getUser());

        return $commande;
    }

}