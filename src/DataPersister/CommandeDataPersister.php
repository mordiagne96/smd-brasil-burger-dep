<?php
namespace App\DataPersister;

use DateTime;
use App\Entity\Commande;
use App\Repository\CommandeRepository;
use App\Services\CalculePrixMenuService;
use Doctrine\ORM\EntityManagerInterface;
use App\Services\GenererNumeroCommandeService;
use App\Services\CalculeMontantCommandeService;
use ApiPlatform\Core\DataPersister\DataPersisterInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class CommandeDataPersister implements DataPersisterInterface
{
    private $entityManager;
    private $service;
    private $genererService;
    private $repo;
    
    public function __construct(EntityManagerInterface $entityManager,TokenStorageInterface $tokenStorage, CalculeMontantCommandeService $service, GenererNumeroCommandeService $genererService, CommandeRepository $repo)
    {
        $this->entityManager = $entityManager;
        $this->service = $service;
        $this->genererService = $genererService;
        $this->repo = $repo;

    }

    public function supports($data, array $context = []): bool
    {
        return $data instanceof Commande;
    }

    public function persist($data, array $context = [])
    {
        $commande = $this->service->calcule($data);
        $numero = $this->genererService->genererNumero($this->repo);
        $commande->setNumeroCommande($numero);
        
        $this->entityManager->persist($commande);
        $this->entityManager->flush();
    }

    public function remove($data, array $context = [])
    {
        // call your persistence layer to delete $data
    }
}