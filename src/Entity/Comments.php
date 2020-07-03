<?php

namespace App\Entity;

use App\Repository\CommentsRepository;
use ApiPlatform\Core\Annotation\ApiResource;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;
use ApiPlatform\Core\Annotation\ApiFilter;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\SearchFilter;

/**
 * @ApiResource(
 *      attributes={
 *          "order"={"published_at": "DESC"}, 
 *          "pagination_client_enabled"=true, 
 *          "pagination_client_items_per_page"=true
 *      },
 *      itemOperations={
 *          "get",
 *          "put"={
 *              "access_control"="is_granted('ROLE_EDITOR') or is_granted('ROLE_SUBSCRIBER') and object.getUser() == user"
 *          }
 *      },
 *      collectionOperations={
 *          "get",
 *          "post"={
 *              "access_control"="is_granted('ROLE_SUBSCRIBER')",
 *              "normalization_context"={"groups"={"get-recipes-comments"}}
 *          },
 *      },
 *      subresourceOperations={
 *         "api_recipes_comments_get_subresource"={
 *              "method"="GET",
 *              "normalization_context"={"groups"={"get-recipes-comments"}}
 *         },
 *      },
 *      denormalizationContext={
 *          "groups"={"post"}
 *      }
 * )
 * @ApiFilter(
 *      SearchFilter::class,
 *      properties={
 *          "recipe.id": "exact"
 *      }
 * )
 * @ORM\Entity(repositoryClass=CommentsRepository::class)
 */
class Comments implements AuthoredEntityInterface
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     * @Groups({"get-recipes-comments"})
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity=Users::class, inversedBy="comments")
     * @ORM\JoinColumn(nullable=false)
     * @Groups({"get-recipes-comments"})
     */
    private $user;

    /**
     * @ORM\ManyToOne(targetEntity=Recipes::class, inversedBy="comments")
     * @Groups({"post"})
     */
    private $recipe;

    /**
     * @ORM\Column(type="smallint", nullable=true)
     * @Groups({"post", "get-recipes-comments"})
     */
    private $rating;

    /**
     * @ORM\Column(type="text")
     * @Groups({"post", "get-recipes-comments"})
     * @Assert\NotBlank(
     *      message="Ce champ est obligatoire"
     * )
     * @Assert\Length(
     *      min=1, 
     *      max=3000,
     *      minMessage = "Votre commentaire doit comporter au moins {{ limit }} caractère",
     *      maxMessage = "Votre commentaire doit comporter un maximum de {{ limit }} caractères"
     * )
     */
    private $content;

    /**
     * @var \DateTime
     * @ORM\Column(type="datetime")
     * @Groups({"get-recipes-comments"})
     */
    private $published_at;

    public function __construct()
    {
        $this->published_at = new \DateTime();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUser(): ?Users
    {
        return $this->user;
    }

    /**
     * @param UserInterface $user
     */
    public function setUser(UserInterface $user): AuthoredEntityInterface
    {
        $this->user = $user;

        return $this;
    }

    public function getRecipe(): ?Recipes
    {
        return $this->recipe;
    }

    public function setRecipe(?Recipes $recipe): self
    {
        $this->recipe = $recipe;

        return $this;
    }

    public function getRating(): ?int
    {
        return $this->rating;
    }

    public function setRating(?int $rating): self
    {
        $this->rating = $rating;

        return $this;
    }

    public function getContent(): ?string
    {
        return $this->content;
    }

    public function setContent(string $content): self
    {
        $this->content = $content;

        return $this;
    }

    public function getPublishedAt(): ?\DateTimeInterface
    {
        return $this->published_at;
    }

    public function setPublishedAt(\DateTimeInterface $published_at): self
    {
        $this->published_at = $published_at;

        return $this;
    }

    public function __toString(): string
    {
        return substr($this->content, 0, 20) . "...";
    }
}
