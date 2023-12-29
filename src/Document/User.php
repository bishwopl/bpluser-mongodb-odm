<?php

namespace BplUserMongoDbODM\Document;

use BplUser\Contract\BplUserInterface;
use BplUserMongoDbODM\Document\UserPasswordReset;
use CirclicalUser\Entity\UserApiToken;
use CirclicalUser\Provider\AuthenticationRecordInterface;
use CirclicalUser\Provider\RoleInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ODM\MongoDB\Mapping\Annotations as ODM;
use Doctrine\ODM\MongoDB\Types\Type;
use JsonException;

#[ODM\Document(collection: "users")]
#[ODM\Index(keys: ["email" => "desc"], options: ["unique" => true])]
class User implements BplUserInterface
{
    #[ODM\Id(strategy: "auto")]
    private ?string $id = null;

    #[ODM\Field(type: "string")]
    private ?string $email = null;

    #[ODM\Field(type: "string", nullable: true)]
    private ?string $firstName = null;

    #[ODM\Field(type: "string", nullable: true)]
    private ?string $lastName = null;

    #[ODM\Field(type: "date_immutable", options: ["default" => "CURRENT_TIMESTAMP"])]
    private \DateTimeImmutable $timeRegistered;

    #[ODM\Field(type: Type::INT, nullable: true)]
    private ?int $state = null;

    #[ODM\ReferenceMany(targetDocument: Role::class, cascade: ["persist"])]
    private Collection $roles;

    #[ODM\ReferenceOne(targetDocument: Authentication::class, cascade: ["persist"], mappedBy: "user")]
    private ?AuthenticationRecordInterface $authenticationRecord = null;

    #[ODM\ReferenceMany(targetDocument: "BplUserMongoDbODM\Document\UserApiToken", cascade: ["all"], mappedBy: "user")]
    private Collection | Array $apiTokens;

    #[ODM\Field(type: "string", nullable: true)]
    private ?string $avatar = null;

    #[ODM\ReferenceOne(targetDocument: UserPasswordReset::class, mappedBy: "user", orphanRemoval: true)]
    private ?UserPasswordReset $userPasswordReset = null;

    public function __construct()
    {
        $this->timeRegistered = new \DateTimeImmutable();
        $this->roles = new ArrayCollection();
        $this->apiTokens = new ArrayCollection();
        $this->userPasswordReset = null;
    }

    public function setEmail(string $email): void
    {
        $this->email = $email;
    }

    public function setFirstName(?string $firstName): void
    {
        $this->firstName = $firstName;
    }

    public function setLastName(?string $lastName): void
    {
        $this->lastName = $lastName;
    }

    public function setState(?int $state): void
    {
        $this->state = $state;
    }

    public function setRoles(Collection $roles): void
    {
        $this->roles = $roles;
    }

    public function setApiTokens(Collection $apiTokens): void
    {
        $this->apiTokens = $apiTokens;
    }

    public function getId(): ?string
    {
        return $this->id;
    }

    public function getFirstName(): ?string
    {
        return $this->firstName;
    }

    public function getLastName(): ?string
    {
        return $this->lastName;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function getRoles(): array
    {
        return $this->roles->getValues();
    }

    public function addRoles(Collection $roles): void
    {
        foreach ($roles as $role) {
            $this->addRole($role);
        }
    }

    public function addRole(RoleInterface $role): void
    {
        if (!$this->roles->contains($role)) {
            $this->roles->add($role);
        }
    }

    public function removeRoles(Collection $roles): void
    {
        foreach ($roles as $role) {
            $this->removeRole($role);
        }
    }

    public function removeRole(RoleInterface $role): void
    {
        if ($this->roles->contains($role)) {
            $this->roles->removeElement($role);
        }
    }

    public function getTimeRegistered(): \DateTimeImmutable
    {
        return $this->timeRegistered;
    }

    public function getPreferredTimezone(): \DateTimeZone
    {
        return new \DateTimeZone('America/New_York');
    }

    public function hasRoleWithName(string $roleName): bool
    {
        foreach ($this->roles as $role) {
            if ($role->getName() === $roleName) {
                return true;
            }
        }

        return false;
    }

    public function hasRole(RoleInterface $searchRole): bool
    {
        return $this->roles->contains($searchRole);
    }

    public function setAuthenticationRecord(?AuthenticationRecordInterface $authentication): void
    {
        $this->authenticationRecord = $authentication;
    }

    public function getAuthenticationRecord(): ?AuthenticationRecordInterface
    {
        return $this->authenticationRecord;
    }

    public function getApiTokens(): ?Collection
    {
        return $this->apiTokens;
    }

    public function addApiToken(UserApiToken $token): void
    {
        $this->apiTokens->add($token);
    }

    public function getApiTokenArray(): array
    {
        return $this->apiTokens->map(static function (UserApiToken $token) {
            return $token->getToken();
        })->getValues();
    }

    /**
     * @throws JsonException
     */
    public function getApiTokensAsJson(): string
    {
        return json_encode($this->getApiTokenArray(), JSON_THROW_ON_ERROR | JSON_PRETTY_PRINT);
    }

    public function findApiTokenWithId(string $uuid): ?UserApiToken
    {
        foreach ($this->apiTokens as $token) {
            if ($token->getToken() === $uuid) {
                return $token;
            }
        }

        return null;
    }

    public function removeApiToken(UserApiToken $token): void
    {
        if ($this->apiTokens->contains($token)) {
            $this->apiTokens->removeElement($token);
        }
    }

    public function getState(): ?int
    {
        return $this->state;
    }

    public function getAvatar(): ?string
    {
        return $this->avatar;
    }

    public function setAvatar(?string $avatar): void
    {
        $this->avatar = $avatar;
    }

    public function getUserPasswordReset(): ?UserPasswordReset
    {
        return $this->userPasswordReset;
    }

    public function setUserPasswordReset(?UserPasswordReset $userPasswordReset): void
    {
        $this->userPasswordReset = $userPasswordReset;
    }
}
