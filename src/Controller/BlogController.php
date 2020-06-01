<?php 

namespace App\Controller;

use App\Entity\Categories;
use App\Entity\Recipes;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Serializer;

/**
 * @Route("/recettes")
 */
class BlogController extends AbstractController {

   /**
    * @Route("/liste/{page}", name="recipe_list", defaults={"page": 5}, requirements={"page"="\d+"})
    */
   public function list($page = 1, Request $request) 
   {

      $limit = $request->get("limit", 10);
      $repository = $this->getDoctrine()->getRepository(Recipes::class);
      $items = $repository->findAll();

      return $this->json(
         [
            "page" => $page,
            "limit" => $limit,
            "data" => array_map(function(Recipes $item) {
               return $this->generateUrl("recipe_by_slug", ["slug" => $item->getSlug()]);
            }, $items)
         ]   
      );
   }

   /**
    * @Route("/{id}", name="recipe_by_id", requirements={"id"="\d+"}, methods={"GET"})
    */
   public function post(Recipes $post) 
   {
      return $this->json($post);
   }

   /**
    * @Route("/{slug}", name="recipe_by_slug", methods={"GET"}))
    */
   public function postByslug(Recipes $post) 
   {
      return $this->json($post);
   }

   /**
    * @Route("/ajouter", name="recipe_add", methods={"POST"})
    */
   public function add(Request $request) 
   {
      /** @var Serializer $serializer */
      $serializer = $this->get("serializer");

      $recipes = $serializer->deserialize($request->getContent(), Recipes::class, "json");

      dump("$recipes");
      die();

      $em = $this->getDoctrine()->getManager();
      $em->persist($recipes);
      $em->flush();

      return $this->json($recipes);
   }
}