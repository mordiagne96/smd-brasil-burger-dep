<?php

namespace App\Entity;

use App\Entity\Menu;
use App\Entity\Burger;
use App\Dto\BurgerMenuInput;
use App\Dto\BurgerMenuOutput;
use Doctrine\ORM\Mapping as ORM;
use App\Repository\BurgerMenuRepository;
use ApiPlatform\Core\Annotation\ApiResource;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: BurgerMenuRepository::class)]
// #[ApiResource(input: BurgerMenuInput::class, output:BurgerMenuOutput::class)]
#[ApiResource()]
class BurgerMenu
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    #[Groups(['Menu:read'])]
    private $id;

    #[ORM\Column(type: 'integer')]
    #[Groups(['Menu:read'])]
    private $quantite;

    #[ORM\ManyToOne(targetEntity: Menu::class, inversedBy: 'burgerMenus')]
    // #[Groups(['Menu:read'])]
    private $menu;

    #[ORM\ManyToOne(targetEntity: Burger::class, inversedBy: 'burgerMenus')]
    #[Groups(['Menu:read'])]
    private $burger;

    public function getId(): ?int
    {
        return $this->id;
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

    public function getMenu(): ?Menu
    {
        return $this->menu;
    }

    public function setMenu(?Menu $menu): self
    {
        $this->menu = $menu;

        return $this;
    }

    public function getBurger(): ?Burger
    {
        return $this->burger;
    }

    public function setBurger(?Burger $burger): self
    {
        $this->burger = $burger;

        return $this;
    }
}
