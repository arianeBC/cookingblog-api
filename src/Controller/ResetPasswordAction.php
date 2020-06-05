<?php

namespace App\Controller;

use ApiPlatform\Core\Validator\ValidatorInterface;
use App\Entity\Users;
use Doctrine\ORM\EntityManagerInterface;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class ResetPasswordAction
{
   /**
    * @var ValidatorInterface
    */
   private $validator;

   /**
     * @var UserPasswordEncoderInterface
     */
   private $userPasswordEncoder;

   /**
    * @var EntityManagerInterface
    */
   private $entityManager;

   /**
    * @var JWTTokenManagerInterface
    */
   private $tokenManager;
   
   public function __construct(
      ValidatorInterface $validator,
      UserPasswordEncoderInterface $userPasswordEncoder,
      EntityManagerInterface $entityManager,
      JWTTokenManagerInterface $tokenManager
   )
   {
      $this->validator = $validator;
      $this->userPasswordEncoder = $userPasswordEncoder;
      $this->entityManager = $entityManager;
      $this->tokenManager = $tokenManager;
   }

   public function __invoke(Users $data)
   {
      //$reset = new ResetPasswordAction();
      //$reset();

      // var_dump(
      //    $data->getNewPassword(),
      //    $data->getNewRetypedPassword(),
      //    $data->getOldPassword(),
      //    $data->getRetypedPassword()
      // );
      // die;

      $this->validator->validate($data);

      $data->setPassword(
         $this->userPasswordEncoder->encodePassword(
            $data, $data->getNewPassword()
         )
      );
      // After password change, old tokens are still valid
      $data->setPasswordChangeDate(time());

      $this->entityManager->flush();

      $token = $this->tokenManager->create($data);

      return new JsonResponse(['token' => $token]);
   }

   // Validator is called after the data is returned from this action so it means that it checks for the user current password after it registered the new password 😑 👍 

   // Entity is persisted automatically only if validation pass
}