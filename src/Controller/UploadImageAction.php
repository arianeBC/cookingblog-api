<?php

namespace App\Controller;

use ApiPlatform\Core\Validator\Exception\ValidationException;
use ApiPlatform\Core\Validator\ValidatorInterface;
use App\Entity\Images;
use App\Form\ImageType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\Request;

class UploadImageAction
{
   /**
    * @Var FormFactoryInterface
    */
   private $formFactory;

   /**
     * @var EntityManagerInterface
     */
   private $entityManager;

   /**
     * @var ValidatorInterface
     */
   private $validator;

   public function __construct(
      FormFactoryInterface $formFactory,
      EntityManagerInterface $entityManager,
      ValidatorInterface $validator
   )
   {
      $this->formFactory = $formFactory;
      $this->entityManager = $entityManager;
      $this->validator = $validator;
   }

   public function __invoke(Request $request)
   {
      // Create a new Image instance
      $image = new Images();

      // Validate the form
      $form = $this->formFactory->create(ImageType::class, $image);
      $form->handleRequest($request);

      if($form->isSubmitted() && $form->isValid()) {
         // Persist the new Image Entity
         $this->entityManager->persist($image);
         $this->entityManager->flush();

         $image->setFile(null);

         return $image;
      }

      // Uploading done for us in background by VichUpoloader...

      // Throw an validation exception, that means something went wrong during form validation

      throw new ValidationException(
         $this->validator->validate($image)
      );
   }
}