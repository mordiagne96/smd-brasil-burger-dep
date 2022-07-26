<?php

namespace App\Dto;
use Symfony\Component\Validator\Constraints as Assert;

final class ZoneInput
{
    /**
     * @var int
     */
    public $id;

    /**
     * @var string
     */
    #[Assert\NotNull(message: "Le nom est Obligatoire")]
    public $nom;
    
    /**
    * @var int
    */
    #[Assert\Type(
        type: 'integer',
        message: 'Cette valeur {{ value }} est n\'est pas accespter a ce {{ type }}.'
    )]
    public $prix;
}