<?php

namespace App\EventListener;

use App\Entity\Users;
use Lexik\Bundle\JWTAuthenticationBundle\Event\AuthenticationSuccessEvent;

class AuthenticationSuccessListener
{
   public function onAuthenticationSuccessResponse(AuthenticationSuccessEvent $event)
   {
      $data = $event->getData();
      $user = $event->getUser();

      if (!$user instanceof Users) {
         return;
      }

      $data['id'] = $user->getId();

      $event->setData($data);
   }
}