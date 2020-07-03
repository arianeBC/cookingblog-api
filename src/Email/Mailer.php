<?php

namespace App\Email;

use App\Entity\Users;
use Swift_Message;
use Twig\Environment;

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
      Environment $twig
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

      $message = (new \Swift_Message("Activez votre compte"))
         ->setFrom("arianebrosseaucote@gmail.com")
         ->setTo($user->getEmail())
         ->setBody($body, "text/html");

      $this->mailer->send($message, $errors);
   }
}