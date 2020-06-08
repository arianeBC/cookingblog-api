<?php

namespace App\Security;

use App\Entity\Users;
use App\Repository\UsersRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class UserConfirmationService
{
   public function __construct(
      UsersRepository $usersRepository,
      EntityManagerInterface $entityManager
   )
   {
      $this->usersRepository = $usersRepository;
      $this->entityManager = $entityManager;
   }

   public function confirmUser(string $confirmationToken)
   {
      $user = $this->usersRepository->findOneBy(
         ['confirmationToken' => $confirmationToken]
      );

      // User was NOT found by confirmation token
      if (!$user) {
         throw new NotFoundHttpException();
      }

      $user->setEnabled(true);
      $user->setConfirmationToken(null);
      $this->entityManager->flush();
   }
}