<?php

namespace App\Email;

use App\Entity\Users;
use Swift_Message;

class Mailer
{
   /**
    * @var \Swift_Mailer
    */
   private $mailer;

   /**
    * @var \Twig_Environment
    */
   private $twig;

   public function __construct(
      \Swift_Mailer $mailer,
      \Twig_Environment $twig
   )
   {
      $this->mailer = $mailer;
      $this->twig = $twig;
   }

   public function sendConfirmationEmail(Users $user)
   {
      $body = $this->twig->render(
         "email/confirmation.html.twig",
         [
            'user' => $user
         ]
      );

      $message = (new Swift_Message("Please confirm your account"))
         ->setFrom("arianebrosseaucote@gmail.com")
         // $user->getEmail()
         ->setTo("arianebrosseaucote@gmail.com")
         // $body
         ->setBody($body, "text/html");

      $this->mailer->send($message);
   }
}