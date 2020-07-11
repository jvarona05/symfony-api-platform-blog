<?php


namespace App\Security;


use App\Entity\User;
use App\Exception\InvalidConfirmationTokenException;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class UserConfirmationService
{
    /**
     * @var EntityManagerInterface
     */
    private $entityManager;
    /**
     * @var UserRepository
     */
    private $userRepository;

    public function __construct(UserRepository $repository, EntityManagerInterface $entityManager)
    {
        $this->userRepository = $repository;
        $this->entityManager = $entityManager;
    }

    public function confirmUser(string $confirmationToken)
    {
        $user = $this->userRepository->findOneBy(['confirmationToken' => $confirmationToken]);

        if(!$user){
            throw new InvalidConfirmationTokenException();
        }

        $user->setEnabled(true);
        $user->setConfirmationToken(null);

        $this->entityManager->flush();
    }
}