<?php
namespace App\Services;

use App\Repository\CommandeRepository;

final class GenererNumeroCommandeService{

    public function genererNumero(CommandeRepository $repo){

        $id = $repo->findOneBy([],['id'=>'desc'])->getId();
        $id++;
        $numero = "COM-00".$id;

        return $numero;

    }
}