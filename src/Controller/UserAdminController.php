<?php

namespace App\Controller;

use EasyCorp\Bundle\EasyAdminBundle\Controller\EasyAdminController as BaseAdminController;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class UserAdminController extends BaseAdminController
{
   /**
    * @var UserPasswordEncoderInterface
    */
   private $passwordEncoder;

   public function __construct(UserPasswordEncoderInterface $passwordEncoder)
   {
      $this->passwordEncoder = $passwordEncoder;
   }

   /**
    * @param Users $entity
    */
   protected function persistEntity($entity)
   {
      $this->encodeUserPassword($entity);
      parent::persistEntity($entity);
   }

   /**
    * @param Users $entity
    */
   protected function updateEntity($entity)
   {
      $this->encodeUserPassword($entity);
      parent::updateEntity($entity);
   }

   /**
    * @param Users $entity
    */
   public function encodeUserPassword($entity): void
   {
      $entity->setPassword(
         $this->passwordEncoder->encodePassword(
            $entity,
            $entity->getPassword()
         )
      );
   }
}