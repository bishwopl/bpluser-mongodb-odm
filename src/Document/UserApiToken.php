<?php

declare(strict_types=1);

namespace BplUserMongoDbODM\Document;

use CirclicalUser\Provider\UserInterface;
use DateTimeImmutable;
use DateTimeZone;
use Doctrine\ODM\MongoDB\Mapping\Annotations as ODM;
use Exception;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;

/**
 * A password-reset token.  This is the thing that you would exchange in a forgot-password email
 * that the user can later consume to trigger a password change.
 */
#[ODM\Document(collection: "users_api_tokens")]
#[ODM\Index(keys: ["user" => "asc"])]
class UserApiToken
{
    use SecretIdPublicUuidTrait;

    public const SCOPE_NONE = 0;

    #[ODM\ReferenceOne(targetDocument: "BplUserMongoDbODM\Document\User", inversedBy: "apiTokens")]
    private UserInterface $user;

    #[ODM\Field(type: "date_immutable")]
    private DateTimeImmutable $creationTime;

    #[ODM\Field(type: "date_immutable", nullable: true)]
    private ?DateTimeImmutable $lastUsed;

    #[ODM\Field(type: "int", options: ["default" => 0, "unsigned" => true])]
    private int $timesUsed;

    #[ODM\Field(type: "int", options: ["default" => 0, "unsigned" => true])]
    private int $scope;

    /**
     * @param int $scope Push a bit-flag integer into this value to resolve scopes
     * @throws Exception
     */
    public function __construct(UserInterface $user, int $scope)
    {
        $this->user = $user;
        $this->creationTime = new DateTimeImmutable('now', new DateTimeZone('UTC'));
        $this->scope = $scope;
        $this->timesUsed = 0;
        $this->uuid = Uuid::uuid4();
    }

    public function addScope(int $newScope): void
    {
        $this->scope |= $newScope;
    }

    public function removeScope(int $removeScope): void
    {
        $this->scope &= ~$removeScope;
    }

    public function hasScope(int $checkForScope): bool
    {
        return ($this->scope & $checkForScope) === $checkForScope;
    }

    public function clearScope(): void
    {
        $this->scope = self::SCOPE_NONE;
    }

    public function getLastUsed(): ?DateTimeImmutable
    {
        return $this->lastUsed;
    }

    public function getTimesUsed(): int
    {
        return $this->timesUsed;
    }

    public function tagUse(): void
    {
        $this->timesUsed++;
        $this->lastUsed = new DateTimeImmutable('now', new DateTimeZone('UTC'));
    }

    public function getUuid(): UuidInterface
    {
        return $this->uuid;
    }

    public function getToken(): string
    {
        return $this->uuid->toString();
    }

    public function getUser(): UserInterface
    {
        return $this->user;
    }
}
