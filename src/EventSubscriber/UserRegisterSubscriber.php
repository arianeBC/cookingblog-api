<?php

namespace App\EventSubscriber;

use ApiPlatform\Core\EventListener\EventPriorities;
use App\Entity\Users;
use App\Security\TokenGenerator;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\ViewEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class UserRegisterSubscriber implements EventSubscriberInterface
{
   /**
    * @var UserPasswordEncoderInterface
    */
   private $passwordEncoder;

   public function __construct(
      UserPasswordEncoderInterface $passwordEncoder,
      TokenGenerator $tokenGenerator
   )
   {
      $this->passwordEncoder = $passwordEncoder;
      $this->tokenGenerator = $tokenGenerator;
   }

   public static function getSubscribedEvents()
   {
      return [
         KernelEvents::VIEW => ['userRegistered', EventPriorities::PRE_WRITE]
      ];
   }

   public function userRegistered(ViewEvent $event)
   {
      $user = $event->getControllerResult();
      $method = $event->getRequest()
            ->getMethod();

      if(!$user instanceof Users || !in_array($method, [Request::METHOD_POST])) {
         return;
      }

      //It is an User, we need to hash password here
      $user->setPassword(
         $this->passwordEncoder->encodePassword($user, $user->getPassword())
      );

      // Create confirmation token
      $user->setConfirmationToken(
         $this->tokenGenerator->getRandomSecureToken()
      );
   }
}