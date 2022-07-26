<?php

namespace App\DataPersister;

use App\Entity\Menu;
use App\Entity\Burger;
use App\Entity\Produit;
use App\Services\DecodeService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use ApiPlatform\Core\DataPersister\ContextAwareDataPersisterInterface;
use App\Services\CalculePrixMenuService;
use App\Services\UploadService;

final class ProduitDataPersister implements ContextAwareDataPersisterInterface
{
    private EntityManagerInterface $entityManager;
    private $service;
    private $uploadService;
    public function __construct(RequestStack $request,EntityManagerInterface $entityManager,CalculePrixMenuService $service,UploadService $uploadService)
    {
        $this->entityManager = $entityManager;
        $this->service = $service;
        $this->uploadService = $uploadService;
    }

    public function supports($data, array $context = []): bool
    {
        // dd($data);
        return $data instanceof Produit;
    }

    public function persist($data, array $context = [])
    {
        // dd($data->getFile());

        if($data instanceof Menu){

           $image = stream_get_contents(fopen($data->getFile()->getRealPath(), 'rb'));
        // dd($menu);
           $data->setImage($image);
        //  dd($data);
           $menu = $this->service->calculer($data);
           $this->entityManager->persist($menu);
           $this->entityManager->flush();
        }

        if($data instanceof Burger){
            // dd($data);
            $burger = $this->uploadService->upload($data);

        //     $image = stream_get_contents(fopen($data->getFile()->getRealPath(), 'rb'));
        //  // dd($menu);
        //     $data->setImage($image);
        //  //  dd($data);
        //     $menu = $this->service->calculer($data);
            $this->entityManager->persist($burger);
            $this->entityManager->flush();
         }
    }

    public function remove($data, array $context = [])
    {
        $data->setEtat("Archiver");
        $this->entityManager->persist($data);
        $this->entityManager->flush();
        // call your persistence layer to delete $data
    }
}