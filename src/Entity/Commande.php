<?php

namespace App\Entity;

use App\Dto\CommandeOutput;
use Doctrine\ORM\Mapping as ORM;
use App\Repository\CommandeRepository;
use ApiPlatform\Core\Annotation\ApiFilter;
use Doctrine\Common\Collections\Collection;
use ApiPlatform\Core\Annotation\ApiResource;
use phpDocumentor\Reflection\Types\Nullable;
use Symfony\Component\HttpFoundation\Response;
use ApiPlatform\Core\Annotation\ApiSubresource;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Serializer\Annotation\Groups;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\SearchFilter;
use App\Dto\CommandeInput;

#[ORM\Entity(repositoryClass: CommandeRepository::class)]
    #[ApiResource(
        collectionOperations:[
            "get"=>[
                'method' => 'get',
                'status' => Response::HTTP_OK,
                'normalization_context' => ['groups' => ['com:read']],
                'denormalization_context' => ['groups' => ['com:write']],
            ],
            "post"=>[
                'status' => Response::HTTP_CREATED,
                'denormalization_context' => ['groups' => ['com:write']],
                'normalization_context' => ['groups' => ['com:read']],
            ]
        ],
        itemOperations:[
            "get",
            "put"

        ]
        ),   
    ]
class Commande
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    #[Groups(['com:write','com:read'])]
    private $id;

    #[ORM\Column(type: 'string', length: 50)]
    #[Groups(['com:write','com:read'])]
    private $numeroCommande;

    #[ORM\Column(type: 'datetime')]
    #[Groups(['com:write','com:read'])]
    private $date;

    #[ORM\Column(type: 'integer')]
    #[Groups(['com:write','com:read'])]
    private $montant;

    #[ORM\Column(type: 'integer')]
    #[Groups(['com:write'])]
    private $etat;

    #[ORM\ManyToOne(targetEntity: Livraison::class, inversedBy: 'commandes')]
    #[ORM\JoinColumn(nullable: true)]
    private $livraison;

    #[ORM\ManyToOne(targetEntity: Client::class, inversedBy: 'commandes')]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(['com:write'])]
    private $client;

    #[ORM\ManyToOne(targetEntity: Gestionnaire::class, inversedBy: 'commandes')]
    private $gestionnaire;

    #[ORM\OneToMany(mappedBy: 'commande', targetEntity: TailleBoissonCommande::class, cascade:['persist'])]
    #[Groups(['com:write'])]
    private $tailleBoissonCommandes;

    #[ORM\ManyToOne(targetEntity: Quartier::class, inversedBy: 'commandes')]
    #[Groups(['com:write'])]
    private $quartier;

    #[ORM\OneToMany(mappedBy: 'commande', targetEntity: BurgerCommande::class)]
    private $burgerCommandes;

    #[ORM\OneToMany(mappedBy: 'commande', targetEntity: MenuCommande::class)]
    private $menuCommandes;

    public function __construct()
    {
        $this->produitCommandes = new ArrayCollection();
        $this->tailleBoissonCommandes = new ArrayCollection();
        $this->burgerCommandes = new ArrayCollection();
        $this->menuCommandes = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNumeroCommande(): ?string
    {
        return $this->numeroCommande;
    }

    public function setNumeroCommande(string $numeroCommande): self
    {
        $this->numeroCommande = $numeroCommande;

        return $this;
    }

    public function getDate(): ?\DateTimeInterface
    {
        return $this->date;
    }

    public function setDate(\DateTimeInterface $date): self
    {
        $this->date = $date;

        return $this;
    }

    public function getMontant(): ?int
    {
        return $this->montant;
    }

    public function setMontant(int $montant): self
    {
        $this->montant = $montant;

        return $this;
    }

    public function getEtat(): ?int
    {
        return $this->etat;
    }

    public function setEtat(int $etat): self
    {
        $this->etat = $etat;

        return $this;
    }
    
    public function getLivraison(): ?Livraison
    {
        return $this->livraison;
    }

    public function setLivraison(?Livraison $livraison): self
    {
        $this->livraison = $livraison;

        return $this;
    }

    

    public function getClient(): ?Client
    {
        return $this->client;
    }

    public function setClient(?Client $client): self
    {
        $this->client = $client;

        return $this;
    }

    public function getGestionnaire(): ?Gestionnaire
    {
        return $this->gestionnaire;
    }

    public function setGestionnaire(?Gestionnaire $gestionnaire): self
    {
        $this->gestionnaire = $gestionnaire;

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
            $tailleBoissonCommande->setCommande($this);
        }

        return $this;
    }

    public function removeTailleBoissonCommande(TailleBoissonCommande $tailleBoissonCommande): self
    {
        if ($this->tailleBoissonCommandes->removeElement($tailleBoissonCommande)) {
            // set the owning side to null (unless already changed)
            if ($tailleBoissonCommande->getCommande() === $this) {
                $tailleBoissonCommande->setCommande(null);
            }
        }

        return $this;
    }

    public function getQuartier(): ?Quartier
    {
        return $this->quartier;
    }

    public function setQuartier(?Quartier $quartier): self
    {
        $this->quartier = $quartier;

        return $this;
    }

    /**
     * @return Collection<int, BurgerCommande>
     */
    public function getBurgerCommandes(): Collection
    {
        return $this->burgerCommandes;
    }

    public function addBurgerCommande(BurgerCommande $burgerCommande): self
    {
        if (!$this->burgerCommandes->contains($burgerCommande)) {
            $this->burgerCommandes[] = $burgerCommande;
            $burgerCommande->setCommande($this);
        }

        return $this;
    }

    public function removeBurgerCommande(BurgerCommande $burgerCommande): self
    {
        if ($this->burgerCommandes->removeElement($burgerCommande)) {
            // set the owning side to null (unless already changed)
            if ($burgerCommande->getCommande() === $this) {
                $burgerCommande->setCommande(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, MenuCommande>
     */
    public function getMenuCommandes(): Collection
    {
        return $this->menuCommandes;
    }

    public function addMenuCommande(MenuCommande $menuCommande): self
    {
        if (!$this->menuCommandes->contains($menuCommande)) {
            $this->menuCommandes[] = $menuCommande;
            $menuCommande->setCommande($this);
        }

        return $this;
    }

    public function removeMenuCommande(MenuCommande $menuCommande): self
    {
        if ($this->menuCommandes->removeElement($menuCommande)) {
            // set the owning side to null (unless already changed)
            if ($menuCommande->getCommande() === $this) {
                $menuCommande->setCommande(null);
            }
        }

        return $this;
    }
}
