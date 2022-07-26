<?php

namespace App\Entity;

use App\Dto\MenuInput;
use App\Dto\MenuOutput;
use Doctrine\ORM\Mapping as ORM;
use App\Repository\MenuRepository;
use ApiPlatform\Core\Annotation\ApiFilter;
use Doctrine\Common\Collections\Collection;
use ApiPlatform\Core\Annotation\ApiResource;
use Symfony\Component\HttpFoundation\Response;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Serializer\Annotation\Groups;

use Doctrine\Common\Annotations\Annotation\Attribute;
use Symfony\Component\Validator\Constraints as Assert;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\SearchFilter;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

#[ORM\Entity(repositoryClass: MenuRepository::class)]
#[ApiResource(
    collectionOperations: [
        "get" => [
            'method' => 'get',
            'status' => Response::HTTP_OK,
            'normalization_context' => ['groups' => ['simple']],
            "security" => "is_granted('PUBLIC_ACCESS')",
            "security_message" => "Vous n'avez pas d'accés à cette Ressource"
        ],
        "post" => [
            'normalization_context' => ['groups' => ['Menu:write:simple']],
            'denormalization_context' => ['groups' => ['Menu:read']],
            'input_formats' => [
                'multipart' => ['multipart/form-data'],
            ],
            // 'input' => MenuInput::class,
            // 'output' => MenuOutput::class
        ]
    ],
    itemOperations: [
        "put" => [
            "security" => "is_granted('ROLE_GESTIONNAIRE')",
            "security_message" => "Vous n'avez pas d'accés à cette Ressource"
        ],
        "get" => [
            "security" => "is_granted('PUBLIC_ACCESS')",
            "security_message" => "Vous n'avez pas d'accés à cette Ressource"
        ]
    ]
    // input:MenuInput::class,
    // output:MenuOutput::class,
    // denormalizationContext: ['groups' => ['write']],
)]
#[ApiFilter(
    SearchFilter::class,
    properties: [
        "nom" => SearchFilter::STRATEGY_PARTIAL,
        "prix" => SearchFilter::STRATEGY_EXACT
    ]
)]
class Menu extends Produit
{
    #[ORM\ManyToOne(targetEntity: PortionFrite::class, inversedBy: 'menus')]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(['Menu:read', 'Menu:write:simple'])]
    private $portionFrite;

    #[ORM\OneToMany(mappedBy: 'menu', targetEntity: BurgerMenu::class, cascade: ["persist"])]
    #[Groups(['Menu:read'])]
    #[Assert\Count(
        min: 1,
        minMessage: "Ajouter un Burger"
    )]
    private $burgerMenus;

    // #[ORM\OneToMany(mappedBy: 'menu', targetEntity: Taille::class)]
    // #[Groups(['Menu:read'])]
    // // #[Assert\NotNull(message:"Taille Obligatoire")]
    // #[Assert\Count(
    //     min: 0
    // )]
    // private $tailles;

    // #[ORM\ManyToOne(targetEntity: Cataloguee::class, inversedBy: 'menus')]
    private $cataloguee;

    #[ORM\OneToMany(mappedBy: 'menu', targetEntity: TailleMenu::class)]
    private $tailleMenus;

    #[ORM\OneToMany(mappedBy: 'menu', targetEntity: MenuCommande::class)]
    private $menuCommandes;

    public function __construct()
    {
        parent::__construct();
        $this->burgerMenus = new ArrayCollection();
        // $this->tailles = new ArrayCollection();
        $this->tailleMenus = new ArrayCollection();
        $this->menuCommandes = new ArrayCollection();
    }

    public function getPortionFrite(): ?PortionFrite
    {
        return $this->portionFrite;
    }

    public function setPortionFrite(?PortionFrite $portionFrite): self
    {
        $this->portionFrite = $portionFrite;

        return $this;
    }


    /**
     * @return Collection<int, BurgerMenu>
     */
    public function getBurgerMenus(): Collection
    {
        return $this->burgerMenus;
    }

    public function addBurgerMenu(BurgerMenu $burgerMenu): self
    {
        if (!$this->burgerMenus->contains($burgerMenu)) {
            $this->burgerMenus[] = $burgerMenu;
            $burgerMenu->setMenu($this);
        }

        return $this;
    }

    public function removeBurgerMenu(BurgerMenu $burgerMenu): self
    {
        if ($this->burgerMenus->removeElement($burgerMenu)) {
            // set the owning side to null (unless already changed)
            if ($burgerMenu->getMenu() === $this) {
                $burgerMenu->setMenu(null);
            }
        }

        return $this;
    }

    // /**
    //  * @return Collection<int, Taille>
    //  */
    // public function getTailles(): Collection
    // {
    //     return $this->tailles;
    // }

    // public function addTaille(Taille $taille): self
    // {
    //     if (!$this->tailles->contains($taille)) {
    //         $this->tailles[] = $taille;
    //         $taille->setMenu($this);
    //     }

    //     return $this;
    // }

    // public function removeTaille(Taille $taille): self
    // {
    //     if ($this->tailles->removeElement($taille)) {
    //         // set the owning side to null (unless already changed)
    //         if ($taille->getMenu() === $this) {
    //             $taille->setMenu(null);
    //         }
    //     }

    //     return $this;
    // // }

    // #[Assert\Callback]
    // public function validate(ExecutionContextInterface $context)
    // {
    //     if(count($this->getTailles()) == 0 && $this->getPortionFrite() == null ){
    //         $context->buildViolation('Ajouté au moins un complément')
    //                  ->addViolation();
    //     }
        
    // }

    #[Assert\Callback]
    public function doublons(ExecutionContextInterface $context)
    {

        if(count($this->getBurgerMenus())  > 1){

            foreach ($this->getBurgerMenus() as $search) {
                $trouve = false;$cpt = 0;
                foreach ($this->getBurgerMenus() as $value) {
                    if($search->getBurger()->getId() == $value->getBurger()->getId()){
                        $cpt++;
                    }
                }

                if($cpt == 2){
                        $context->buildViolation('Erreur!! verifier les doublons!')
                        ->addViolation();
                    break;
                }
            }
        }
        

    }

    public function getCataloguee(): ?Cataloguee
    {
        return $this->cataloguee;
    }

    public function setCataloguee(?Cataloguee $cataloguee): self
    {
        $this->cataloguee = $cataloguee;

        return $this;
    }

    /**
     * @return Collection<int, TailleMenu>
     */
    public function getTailleMenus(): Collection
    {
        return $this->tailleMenus;
    }

    public function addTailleMenu(TailleMenu $tailleMenu): self
    {
        if (!$this->tailleMenus->contains($tailleMenu)) {
            $this->tailleMenus[] = $tailleMenu;
            $tailleMenu->setMenu($this);
        }

        return $this;
    }

    public function removeTailleMenu(TailleMenu $tailleMenu): self
    {
        if ($this->tailleMenus->removeElement($tailleMenu)) {
            // set the owning side to null (unless already changed)
            if ($tailleMenu->getMenu() === $this) {
                $tailleMenu->setMenu(null);
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
            $menuCommande->setMenu($this);
        }

        return $this;
    }

    public function removeMenuCommande(MenuCommande $menuCommande): self
    {
        if ($this->menuCommandes->removeElement($menuCommande)) {
            // set the owning side to null (unless already changed)
            if ($menuCommande->getMenu() === $this) {
                $menuCommande->setMenu(null);
            }
        }

        return $this;
    }
}
