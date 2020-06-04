<?php

namespace App\Entity;

use App\Repository\UsersRepository;
use ApiPlatform\Core\Annotation\ApiResource;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\UserInterface;
#use Symfony\Component\Security\Core\Validator\Constraints\UserPassword;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;
#use App\Controller\ResetPasswordAction;

/**
 * @ApiResource(
 *      itemOperations={
 *          "get"={
 *              "access_control"="is_granted('IS_AUTHENTICATED_FULLY')",
 *              "normalization_context"={
 *                  "groups"={"get"}
 *              }
 *          },
 *          "put"={
 *              "access_control"="is_granted('IS_AUTHENTICATED_FULLY') and object == user",
 *              "denormalization_context"={
 *                  "groups"={"put"}
 *              },
 *              "normalization_context"={
 *                  "groups"={"get"}
 *              }
 *          }
 *      },
 *      collectionOperations={
 *          "post"={
 *              "denormalization_context"={
 *                  "groups"={"post"}
 *              },
 *              "normalization_context"={
 *                  "groups"={"get"}
 *              }
 *          }
 *      },
 * )
 * @ORM\Entity(repositoryClass=UsersRepository::class)
 * @UniqueEntity(
 *      "email",
 *      message="Cette adresse e-mail est déjà enregistrée",
 *      groups={"post"}
 * )
 * @UniqueEntity(
 *      "username",
 *      message="Ce nom d'utilisateur est déjà utilisé",
 *      groups={"post"}
 * )
 */
class Users implements UserInterface
{
    const ROLE_SUBSCRIBER = "ROLE_SUBSCRIBER";
    const ROLE_WRITER = "ROLE_WRITER";
    const ROLE_EDITOR = "ROLE_EDITOR";
    const ROLE_ADMIN = "ROLE_ADMIN";
    const ROLE_SUPERADMIN = "ROLE_SUPERADMIN";

    const DEFAULT_ROLES = [self::ROLE_SUBSCRIBER];

    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     * @Groups({"get"})
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=40)
     * @Groups({"get", "post", "get-recipes-comments"})
     * @Assert\NotBlank(
     *      message="Ce champ est obligatoire"
     * )
     * @Assert\Length(
     *      min=3, 
     *      max=40,
     *      minMessage = "Ce champ doit comporter au moins {{ limit }} caractères",
     *      maxMessage = "Ce champ doit comporter un maximum de {{ limit }} caractères"
     * )
     */
    private $usergroup;

    /**
     * @ORM\Column(type="string", length=60, unique=true)
     * @Groups({"get", "post", "put", "get-recipes-comments"})
     * @Assert\NotBlank(
     *      message="Ce champ est obligatoire"
     * )
     * @Assert\Length(
     *      min=3, 
     *      max=60,
     *      minMessage = "Votre nom doit comporter au moins {{ limit }} caractères",
     *      maxMessage = "Votre nom doit comporter un maximum de {{ limit }} caractères"
     * )
     */
    private $username;

    /**
     * @ORM\Column(type="string", length=255, unique=true)
     * @Groups({"post", "put", "get-admin", "get-owner"})
     * @Assert\NotBlank(
     *      message="Ce champ est obligatoire"
     * )
     * @Assert\Email(
     *      message="Veuillez entrer une adresse e-mail valide"
     * )
     * @Assert\Length(
     *      min=6, 
     *      max=255,
     *      minMessage = "Votre email doit comporter au moins {{ limit }} caractères",
     *      maxMessage = "Votre email doit comporter un maximum de {{ limit }} caractères"
     * )
     */
    private $email;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups({"put", "post"})
     * @Assert\NotBlank(
     *      message="Ce champ est obligatoire",
     *      groups={"post"}
     * )
     * @Assert\Regex(
     *      pattern="/(?=.*[A-Z])(?=.*[a-z])(?=.*[0-9]).{8,}/",
     *      message="Votre mot de passe doit comporter au moins 8 caractères et contenir une majuscule, une minuscule et un chiffre",
     *      groups={"post"}
     * )
     */
    private $password;

    /**
     * @Groups({"put", "post"})
     * @Assert\NotBlank(
     *      message="Ce champ est obligatoire"
     * )
     * @Assert\Expression(
     *      "this.getPassword() === this.getRetypedPassword()",
     *      message="Les mots de passes que vous avez saisis ne correspondent pas"
     * )
     */
    private $retypedPassword;

    /**
     * @var \DateTime
     * @ORM\Column(type="datetime", nullable=false)
     */
    private $created_at;

    /**
     * @ORM\OneToMany(targetEntity=Comments::class, mappedBy="user")
     * @Groups({"get"})
     */
    private $comments;

    /**
     * @ORM\Column(type="simple_array", length=200, nullable=false)
     * @Groups({"get-admin", "get-owner"})
     */
    private $roles;

    public function __construct()
    {
        $this->created_at = new \DateTime();
        $this->comments = new ArrayCollection();
        $this->roles = self::DEFAULT_ROLES;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUsergroup(): ?string
    {
        return $this->usergroup;
    }

    public function setUsergroup(string $usergroup): self
    {
        $this->usergroup = $usergroup;

        return $this;
    }

    public function getUsername(): ?string
    {
        return $this->username;
    }

    public function setUsername(string $username): self
    {
        $this->username = $username;

        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

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

    public function getRoles(): array
    {
        return $this->roles;
    }

    public function setRoles(array $roles)
    {
        $this->roles = $roles;
    }

    public function getSalt()
    {
        return null;
    }

    public function eraseCredentials()
    {
        
    }

    public function getRetypedPassword()
    {
        return $this->retypedPassword;
    }

    public function setRetypedPassword($retypedPassword): void
    {
        $this->retypedPassword = $retypedPassword;
    }

    public function __toString(): string
    {
        return $this->usergroup;
    }
}
