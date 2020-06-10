<?php

namespace App\Entity;

use App\Repository\UsersRepository;
use ApiPlatform\Core\Annotation\ApiResource;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\Validator\Constraints\UserPassword;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;
use App\Controller\ResetPasswordAction;

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
 *          },
 *          "put-reset-password"={
 *              "access_control"="is_granted('IS_AUTHENTICATED_FULLY') and object == user",
 *              "method"="PUT",
 *              "path"="/users/{id}/reset-password",
 *              "controller"=ResetPasswordAction::class,
 *              "denormalization_context"={
 *                  "groups"={"put-reset-password"}
 *              },
 *              "validation_groups"={"put-reset-password"}
 *          }
 *      },
 *      collectionOperations={
 *          "post"={
 *              "denormalization_context"={
 *                  "groups"={"post"}
 *              },
 *              "normalization_context"={
 *                  "groups"={"get"}
 *              },
 *              "validation_groups"={"post"}
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
     *      message="Ce champ est obligatoire",
     *      groups={"post"}
     * )
     * @Assert\Length(
     *      min=3, 
     *      max=40,
     *      minMessage = "Ce champ doit comporter au moins {{ limit }} caractères",
     *      maxMessage = "Ce champ doit comporter un maximum de {{ limit }} caractères",
     *      groups={"post", "put"}
     * )
     */
    private $usergroup;

    /**
     * @ORM\Column(type="string", length=60, unique=true)
     * @Groups({"get", "post", "put", "get-recipes-comments"})
     * @Assert\NotBlank(
     *      message="Ce champ est obligatoire",
     *      groups={"post"}
     * )
     * @Assert\Length(
     *      min=3, 
     *      max=60,
     *      minMessage = "Votre nom doit comporter au moins {{ limit }} caractères",
     *      maxMessage = "Votre nom doit comporter un maximum de {{ limit }} caractères",
     *      groups={"post"}
     * )
     */
    private $username;

    /**
     * @ORM\Column(type="string", length=255, unique=true)
     * @Groups({"post", "put", "get-admin", "get-owner"})
     * @Assert\NotBlank(
     *      message="Ce champ est obligatoire",
     *      groups={"post"}
     * )
     * @Assert\Email(
     *      message="Veuillez entrer une adresse e-mail valide",
     *      groups={"post", "put"}
     * )
     * @Assert\Length(
     *      min=6, 
     *      max=255,
     *      minMessage = "Votre email doit comporter au moins {{ limit }} caractères",
     *      maxMessage = "Votre email doit comporter un maximum de {{ limit }} caractères",
     *      groups={"post", "put"}
     * )
     */
    private $email;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups({"post"})
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
     * @Groups({"post"})
     * @Assert\NotBlank(
     *      message="Ce champ est obligatoire",
     *      groups={"post"}
     * )
     * @Assert\Expression(
     *      "this.getPassword() === this.getRetypedPassword()",
     *      message="Les mots de passes que vous avez saisis ne correspondent pas",
     *      groups={"post"}
     * )
     */
    private $retypedPassword;

    /**
     * @Groups({"put-reset-password"})
     * @Assert\NotBlank(
     *      message="Ce champ est obligatoire",
     *      groups={"put-reset-password"}
     * )
     * @Assert\Regex(
     *      pattern="/(?=.*[A-Z])(?=.*[a-z])(?=.*[0-9]).{8,}/",
     *      message="Votre mot de passe doit comporter au moins 8 caractères et contenir une majuscule, une minuscule et un chiffre",
     *      groups={"put-reset-password"}
     * )
     */
    private $newPassword;

    /**
     * @Groups({"put-reset-password"})
     * @Assert\NotBlank(
     *      message="Ce champ est obligatoire",
     *      groups={"put-reset-password"}
     * )
     * @Assert\Expression(
     *      "this.getNewPassword() === this.getNewRetypedPassword()",
     *      message="Les mots de passes que vous avez saisis ne correspondent pas",
     *      groups={"put-reset-password"}
     * )
     */
    private $newRetypedPassword;

    /**
     * @Groups({"put-reset-password"})
     * @Assert\NotBlank(
     *      message="Ce champ est obligatoire",
     *      groups={"put-reset-password"}
     * )
     * @UserPassword(
     *      message="Veuillez saisir votre ancien mot de passe",
     *      groups={"put-reset-password"}
     * )
     */
    private $oldPassword;

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

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $passwordChangeDate;

    /**
     * @ORM\Column(type="boolean")
     */
    private $enabled;

    /**
     * @ORM\Column(type="string", length=40, nullable=true)
     */
    private $confirmationToken;

    public function __construct()
    {
        $this->created_at = new \DateTime();
        $this->comments = new ArrayCollection();
        $this->roles = self::DEFAULT_ROLES;
        $this->enabled = false;
        $this->confirmationToken = null;
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

    public function getNewPassword(): ?string
    {
        return $this->newPassword;
    }

    public function setNewPassword($newPassword): void
    {
        $this->newPassword = $newPassword;
    }

    public function getNewRetypedPassword(): ?string
    {
        return $this->newRetypedPassword;
    }

    public function setNewRetypedPassword($newRetypedPassword): void
    {
        $this->newRetypedPassword = $newRetypedPassword;
    }

    public function getOldPassword(): ?string
    {
        return $this->oldPassword;
    }

    public function setOldPassword($oldPassword): void
    {
        $this->oldPassword = $oldPassword;
    }

    public function getPasswordChangeDate()
    {
        return $this->passwordChangeDate;
    }

    public function setPasswordChangeDate($passwordChangeDate): void
    {
        $this->passwordChangeDate = $passwordChangeDate;
    }

    public function getEnabled()
    {
        return $this->enabled;
    }

    public function setEnabled($enabled): void
    {
        $this->enabled = $enabled;
    }

    public function getConfirmationToken()
    {
        return $this->confirmationToken;
    }

    public function setConfirmationToken($confirmationToken): void
    {
        $this->confirmationToken = $confirmationToken;
    }

    public function __toString(): string
    {
        return $this->usergroup;
    }
}
