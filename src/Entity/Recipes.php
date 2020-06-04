<?php

namespace App\Entity;

use App\Repository\RecipesRepository;
use ApiPlatform\Core\Annotation\ApiResource;
use ApiPlatform\Core\Annotation\ApiSubresource;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ApiResource(
 *      itemOperations={
 *          "get",
 *          "put"={
 *              "access_control"="is_granted('ROLE_EDITOR') or is_granted('ROLE_WRITER')"
 *          }
 *      },
 *      collectionOperations={
 *          "get",
 *          "post"={
 *              "access_control"="is_granted('ROLE_WRITER')"
 *          }
 *      },
 *      denormalizationContext={
 *          "groups"={"post"}
 *      }
 * )
 * @ORM\Entity(repositoryClass=RecipesRepository::class)
 */
class Recipes
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity=Categories::class, inversedBy="recipes")
     * @ORM\JoinColumn(nullable=false)
     * @Assert\NotBlank(
     *      message="Ce champ est obligatoire"
     * )
     * @Groups({"post"})
     */
    private $category;

    /**
     * @ORM\Column(type="string", length=60, nullable=true)
     * @Assert\Length(
     *      min=3, 
     *      max=60,
     *      minMessage = "Ce champ doit comporter au moins {{ limit }} caractères",
     *      maxMessage = "Ce champ doit comporter un maximum de {{ limit }} caractères"
     * )
     * @Groups({"post"})
     */
    private $theme;

    /**
     * @ORM\Column(type="string", length=60)
     * @Assert\NotBlank(
     *      message="Ce champ est obligatoire"
     * )
     * @Assert\Length(
     *      min=3, 
     *      max=60,
     *      minMessage = "Ce champ doit comporter au moins {{ limit }} caractères",
     *      maxMessage = "Ce champ doit comporter un maximum de {{ limit }} caractères"
     * )
     * @Groups({"post"})
     */
    private $title;

    /**
     * @ORM\Column(type="text")
     * @Assert\NotBlank(
     *      message="Ce champ est obligatoire"
     * )
     * @Groups({"post"})
     */
    private $ingredients;

    /**
     * @ORM\Column(type="text")
     * @Assert\NotBlank(
     *      message="Ce champ est obligatoire"
     * )
     * @Assert\Length(
     *      min=20, 
     *      minMessage = "Ce champ doit comporter au moins {{ limit }} caractères"
     * )
     * @Groups({"post"})
     */
    private $content;

    /**
     * @ORM\Column(type="string", length=70)
     * @Assert\NotBlank(
     *      message="Ce champ est obligatoire"
     * )
     * @Assert\Length(
     *      min=20, 
     *      minMessage = "Ce champ doit comporter au moins {{ limit }} caractères"
     * )
     * @Groups({"post"})
     */
    private $image;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Assert\NotBlank(
     *      message="Ce champ est obligatoire"
     * )
     * @Groups({"post"})
     */
    private $slug;

    /**
     * @var \DateTime
     * @ORM\Column(type="datetime")
     */
    private $created_at;

    /**
     * @ORM\OneToMany(targetEntity=Comments::class, mappedBy="recipe")
     * @ApiSubresource()
     */
    private $comments;

    public function __construct()
    {
        $this->created_at = new \DateTime();
        $this->comments = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCategory(): ?Categories
    {
        return $this->category;
    }

    public function setCategory(?Categories $category): self
    {
        $this->category = $category;

        return $this;
    }

    public function getTheme(): ?string
    {
        return $this->theme;
    }

    public function setTheme(?string $theme): self
    {
        $this->theme = $theme;

        return $this;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): self
    {
        $this->title = $title;

        return $this;
    }

    public function getIngredients(): ?string
    {
        return $this->ingredients;
    }

    public function setIngredients(string $ingredients): self
    {
        $this->ingredients = $ingredients;

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

    public function getImage(): ?string
    {
        return $this->image;
    }

    public function setImage(string $image): self
    {
        $this->image = $image;

        return $this;
    }

    public function getSlug(): ?string
    {
        return $this->slug;
    }

    public function setSlug(?string $slug): self
    {
        $this->slug = $slug;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->created_at;
    }

    public function setCreatedAt(\DateTimeInterface $created_at): self
    {
        $this->created_at = $created_at;

        return $this;
    }

    /**
     * @return Collection|Comments[]
     */
    public function getComments(): Collection
    {
        return $this->comments;
    }

    public function addComment(Comments $comment): self
    {
        if (!$this->comments->contains($comment)) {
            $this->comments[] = $comment;
            $comment->setRecipe($this);
        }

        return $this;
    }

    public function removeComment(Comments $comment): self
    {
        if ($this->comments->contains($comment)) {
            $this->comments->removeElement($comment);
            // set the owning side to null (unless already changed)
            if ($comment->getRecipe() === $this) {
                $comment->setRecipe(null);
            }
        }

        return $this;
    }
}
