<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use App\Dto\TailleBoissonInput;
use Doctrine\ORM\Mapping as ORM;
use App\Repository\TailleBoissonRepository;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: TailleBoissonRepository::class)]
#[ApiResource(
    // input:TailleBoissonInput::class
    collectionOperations:[
        "get",
        "post"=>[
            "normalization_context"=> ['groups' => ['taille:write']],
            "denormalization_context"=> ['groups' => ['taille:read']]
        ]
    ]
)]
class TailleBoisson
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    #[Groups(['Menu:read','Menu:write:simple','taille:read','com:write'])]
    private $id;

    #[ORM\ManyToOne(targetEntity: Taille::class, inversedBy: 'tailleBoissons')]
    #[Groups(['Menu:read','Menu:write:simple','taille:read','com:write'])]
    private $taille;

    #[ORM\Column(type: 'integer')]
    #[Groups(['Menu:read','Menu:write:simple','taille:read'])]
    private $quantite;

    #[ORM\ManyToOne(targetEntity: Boisson::class, inversedBy: 'tailleBoissons')]
    #[Groups(['Menu:read','Menu:write:simple','taille:read'])]
    private $boisson;

    #[ORM\OneToMany(mappedBy: 'tailleBoisson', targetEntity: TailleBoissonCommande::class)]
    private $tailleBoissonCommandes;

    public function __construct()
    {
        $this->menus = new ArrayCollection();
        $this->tailleBoissonCommandes = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTaille(): ?Taille
    {
        return $this->taille;
    }

    public function setTaille(?Taille $taille): self
    {
        $this->taille = $taille;

        return $this;
    }

    public function getQuantite(): ?int
    {
        return $this->quantite;
    }

    public function setQuantite(int $quantite): self
    {
        $this->quantite = $quantite;

        return $this;
    }

    public function getBoisson(): ?Boisson
    {
        return $this->boisson;
    }

    public function setBoisson(?Boisson $boisson): self
    {
        $this->boisson = $boisson;

        return $this;
    }

    /**
     * @return Collection<int, TailleBoissonCommande>
     */
    public function getTailleBoissonCommandes(): Collection
    {
        return $this->tailleBoissonCommandes;
    }

    public function addTailleBoissonCommande(TailleBoissonCommande $tailleBoissonCommande): self
    {
        if (!$this->tailleBoissonCommandes->contains($tailleBoissonCommande)) {
            $this->tailleBoissonCommandes[] = $tailleBoissonCommande;
            $tailleBoissonCommande->setTailleBoisson($this);
        }

        return $this;
    }

    public function removeTailleBoissonCommande(TailleBoissonCommande $tailleBoissonCommande): self
    {
        if ($this->tailleBoissonCommandes->removeElement($tailleBoissonCommande)) {
            // set the owning side to null (unless already changed)
            if ($tailleBoissonCommande->getTailleBoisson() === $this) {
                $tailleBoissonCommande->setTailleBoisson(null);
            }
        }

        return $this;
    }
}
