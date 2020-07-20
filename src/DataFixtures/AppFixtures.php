<?php

namespace App\DataFixtures;

use App\Entity\Users;
use App\Entity\Categories;
use App\Entity\Recipes;
use App\Entity\Comments;
use App\Security\TokenGenerator;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class AppFixtures extends Fixture
{
    /**
     * @var Faker\Factory
     */
    private $faker;

    private const USER = [
        [
            "usergroup" => "Super Admin",
            "username" => "superadmin",
            "email" => "arianebrosseaucote@gmail.com",
            "password" => "Qwerty0000",
            "retypedPassword" => "Qwerty0000",
            "roles" => [Users::ROLE_SUPERADMIN],
            "enabled" => true
        ],
        [
            "usergroup" => "John Doe",
            "username" => "John_Doe",
            "email" => "johndoe@gmail.com",
            "password" => "Qwerty0000",
            "retypedPassword" => "Qwerty0000",
            "roles" => [Users::ROLE_ADMIN],
            "enabled" => true
        ],
        [
            "usergroup" => "Rob Smith",
            "username" => "Rob_Smith",
            "email" => "robsmith@gmail.com",
            "password" => "Qwerty0000",
            "retypedPassword" => "Qwerty0000",
            "roles" => [Users::ROLE_WRITER],
            "enabled" => true
        ],
        [
            "usergroup" => "Jenny Rowling",
            "username" => "Jenny_Rowling",
            "email" => "jennyrowling@gmail.com",
            "password" => "Qwerty0000",
            "retypedPassword" => "Qwerty0000",
            "roles" => [Users::ROLE_EDITOR],
            "enabled" => true
        ],
        [
            "usergroup" => "Jedi Knight",
            "username" => "Jedi_Knight",
            "email" => "jediknight@gmail.com",
            "password" => "Qwerty0000",
            "retypedPassword" => "Qwerty0000",
            "roles" => [Users::ROLE_SUBSCRIBER],
            "enabled" => true
        ],
    ];

    public function __construct(
        UserPasswordEncoderInterface $passwordEncoder,
        TokenGenerator $tokenGenerator
    )
    {
        $this->passwordEncoder = $passwordEncoder;
        $this->faker = \Faker\Factory::create();
        $this->tokenGenerator = $tokenGenerator;
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
        foreach (self::USER as $userFixture) {
            $user = new Users();
            $user->setUsergroup($userFixture['usergroup']);
            $user->setUserName($userFixture['username']);
            $user->setEmail($userFixture['email']);
    
            $user->setPassword($this->passwordEncoder->encodePassword(
                $user,
                $userFixture['password']
            ));
            $user->setRoles($userFixture['roles']);

            $user->setCreatedAt(new \DateTime());
            $user->setEnabled($userFixture['enabled']);

            if(!$userFixture['enabled']) {
                $user->setConfirmationToken(
                    $this->tokenGenerator->getRandomSecureToken()
                );
            }

            $this->addReference("user_" . $userFixture['username'], $user);
            $manager->persist($user);
        }

        $manager->flush();
    }

    public function loadCategories(ObjectManager $manager)
    {
        $category = new Categories();
        $category->setName("Plats");
        $category->setCreatedAt(new \DateTime());

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
            $recipe->setSlug($this->faker->slug);
            $recipe->setCreatedAt($this->faker->DateTimeThisYear);

            $this->setReference("recipe_$i", $recipe);

            $manager->persist($recipe);
        }

        $manager->flush();
    }

    public function loadComments(ObjectManager $manager)
    {
        for ($i = 0; $i < 50; $i++) {
            for ($j = 0; $j < rand(1, 10); $j++) {
                $comment = new Comments();
                $comment->setRating("5");
                $comment->setContent($this->faker->realText());
                $comment->setPublishedAt($this->faker->dateTimeThisYear);

                $authorReference = $this->getRandomUserReference($comment);

                $comment->setUser($authorReference);
                $comment->setRecipe($this->getReference("recipe_$i"));

                $manager->persist($comment);
            }
        }

        $manager->flush();
    }

    protected function getRandomUserReference($entity): Users
    {
        $randomUser = self::USER[rand(0, 4)];

        if ($entity instanceof Recipes && !count(
            array_intersect(
                $randomUser['roles'], 
                [Users::ROLE_SUPERADMIN, Users::ROLE_ADMIN, Users::ROLE_WRITER]
            )
        )) {
            return $this->getRandomUserReference($entity);
        }

        if ($entity instanceof Comments && !count(
            array_intersect(
                $randomUser['roles'], 
                [
                    Users::ROLE_SUPERADMIN, 
                    Users::ROLE_ADMIN, 
                    Users::ROLE_WRITER, 
                    Users::ROLE_SUBSCRIBER
                ]
            )
        )) {
            return $this->getRandomUserReference($entity);
        }

        return $this->getReference(
            "user_".$randomUser['username']
        );
    }
}
