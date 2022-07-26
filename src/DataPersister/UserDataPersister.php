<?php

namespace App\DataPersister;

use App\Entity\User;
use App\Entity\BlogPost;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Mailer\MailerInterface;
use ApiPlatform\Core\DataPersister\DataPersisterInterface;
use ApiPlatform\Core\DataPersister\ContextAwareDataPersisterInterface;
use App\Controller\MailerController;
use App\Services\FileService;
use App\Services\MailerService;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Security\Csrf\TokenStorage\TokenStorageInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Twig\Environment;

class UserDataPersister implements DataPersisterInterface
{
    private UserPasswordHasherInterface $passwordHasher;
    private EntityManagerInterface $entityManager;
    private ?TokenInterface $token;
    private $fileService;
    // private Environment $twig;
    public function __construct(UserPasswordHasherInterface $passwordHasher,EntityManagerInterface $entityManager,TokenStorageInterface $tokenStorage)
    {
        $this->passwordHasher= $passwordHasher;
        $this->entityManager = $entityManager;
    }

    public function supports($data, array $context = []): bool
    {
        return $data instanceof User; 
    }

    public function persist($data, array $context = [])
    {
            $hashedPassword = $this->passwordHasher->hashPassword(
            $data,
            'passer'
            );
            $data->setPassword($hashedPassword);

            // $service = new MailerService($this->mailer, $this->twig);
            // $service->sendEmail($this->mailer, $this->twig);

            $this->entityManager->persist($data);
            $this->entityManager->flush();
    }

    public function remove($data, array $context = [])
    {
        // call your persistence layer to delete $data
    }
}