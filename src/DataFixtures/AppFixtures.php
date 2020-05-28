<?php

namespace App\DataFixtures;

use App\Entity\Users;
use App\Entity\Categories;
use App\Entity\Recipes;
use App\Entity\Comments;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class AppFixtures extends Fixture
{
    /**
     * @var Faker\Factory
     */
    private $faker;

    public function __construct(UserPasswordEncoderInterface $passwordEncoder)
    {
        $this->passwordEncoder = $passwordEncoder;
        $this->faker = \Faker\Factory::create();
    }
    
    /**
     * Load data fixtures with passed EntityManager
     * @param ObjectManager $manager
     */
    public function load(ObjectManager $manager)
    {
        $this->loadUsers($manager);
        $this->loadCategories($manager);
        $this->loadRecipes($manager);
        $this->loadComments($manager);
    }

    public function loadUsers(ObjectManager $manager)
    {
        $user = new Users();
        $user->setUsergroup("Administrator");
        $user->setUserName("Ariane Desvals");
        $user->setEmail("desvalsariane@gmail.com");

        $user->setPassword($this->passwordEncoder->encodePassword(
            $user,
            "qwerty"
        ));

        $user->setCreatedAt(new \DateTime("2020-05-27 18:25:00"));

        $this->addReference("user_id", $user);

        $manager->persist($user);
        $manager->flush();
    }

    public function loadCategories(ObjectManager $manager)
    {
        $category = new Categories();
        $category->setName("Plats");
        $category->setCreatedAt(new \DateTime("2020-05-27 18:25:00"));

        $this->addReference("category_id", $category);

        $manager->persist($category);
        $manager->flush();
    }

    public function loadRecipes(ObjectManager $manager)
    {
        $category = $this->getReference("category_id");

        for($i = 0; $i < 50; $i++) {
            $recipe = new Recipes();
            $recipe->setCategory($category);
            $recipe->setTheme("Vegan");
            $recipe->setTitle($this->faker->realText(30));
            $recipe->setIngredients($this->faker->realText(60));
            $recipe->setContent($this->faker->realText);
            $recipe->setImage($this->faker->text(20));
            $recipe->setSlug($this->faker->slug);
            $recipe->setCreatedAt($this->faker->DateTimeThisYear);

            $this->setReference("recipe_$i", $recipe);

            $manager->persist($recipe);
        }

        $manager->flush();

        // $category = $this->getReference("category_id");
        // $theme = $this->getReference("theme_id");

        // $recipe = new Recipes();
        // $recipe->setCategoryId($category);
        // $recipe->addThemeId($theme);
        // $recipe->setTags("Légumineuses");
        // $recipe->setTitle("Cari de lentilles et pommes de terres");
        // $recipe->setIngredients("1 gros oignon, haché finement - 30 ml (2 c. à soupe) d'huile d'olive - 2 gousses d'ail, hachées finement - 15 ml (1 c. à soupe) de garam masala ou 15 ml (1 c. à soupe) de poudre de cari - 1.125 litre (4 tasses) de bouillon de légumes ou d'eau - 1 litre (4 tasses) de pommes de terre coupées en cubes - 1/2 litre (2 tasses) de carottes coupées en cubes - 375 ml (11/2 tasse) de lentilles vertes sèches, rincées - 1 boîte de 398 ml (14 oz) de tomates en dés - Feuilles de coriandre fraîche au goût (facultatif) - Sel et poivre");
        // $recipe->setContent("1. Dans une grande casserole, dorer l'oignon dans l'huile. Saler et poivrer. Ajouter l'ail, le garam masala et cuire 1 minute. 2. Ajouter le reste des ingrédients à l'exception de la coriandre et porter à ébullition. Laisser mijoter doucement, à découvert, environ 35 minutes ou jusqu'à ce que les lentilles soient tendres. Rectifier l'assaisonnement. Servir sur du riz basmati et garnir de coriandre.");
        // $recipe->setRating("5");
        // $recipe->setImage("Cari-de-lentilles-et-pommes-de-terres.jpg");
        // $recipe->setSlug("cari-de-lentilles-et-pommes-de-terres");
        // $recipe->setCreatedAt(new \DateTime("2020-05-27 18:25:00"));

        // $manager->persist($recipe);

        // $manager->flush();
    }

    public function loadComments(ObjectManager $manager)
    {
        for ($i = 0; $i < 50; $i++) {
            for ($j = 0; $j < rand(1, 10); $j++) {
                $comment = new Comments();
                $comment->setUser($this->getReference("user_id"));
                $comment->setRecipe($this->getReference("recipe_$i"));
                $comment->setRating("5");
                $comment->setContent($this->faker->realText());
                $comment->setPublishedAt($this->faker->DateTimeThisYear);

                $manager->persist($comment);
            }
        }

        $manager->flush();
    }

}
