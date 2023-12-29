<?php

declare(strict_types=1);

namespace BplUserMongoDbODM\Document;

use CirclicalUser\Provider\AuthenticationRecordInterface;
use CirclicalUser\Provider\UserInterface;
use DateTimeImmutable;
use Doctrine\ODM\MongoDB\Mapping\Annotations as ODM;
use Doctrine\ODM\MongoDB\Types\Type;

use function base64_decode;
use function base64_encode;

#[ODM\Document(collection: "users_auth")]
#[ODM\Index(keys: ["username" => "desc"], options: ["unique" => true])]
class Authentication implements AuthenticationRecordInterface
{
    #[ODM\Id(strategy: "auto")]
    private $id;
    
    #[ODM\ReferenceOne(targetDocument: User::class, inversedBy: "authenticationRecord")]
    private UserInterface $user;

    #[ODM\Field(type: "string", nullable: false)]
    private string $username;

    #[ODM\Field(type: "string", nullable: false)]
    private string $hash;

    #[ODM\Field(type: "string", nullable: true, options: ["fixed" => true])]
    private string $session_key;

    #[ODM\Field(type: "string", nullable: true, options: ["fixed" => true])]
    private string $reset_hash;

    #[ODM\Field(type: "date_immutable", nullable: true)]
    private DateTimeImmutable $reset_expiry;

    public function __construct(UserInterface $user, string $username, string $hash, string $encodedSessionKey)
    {
        $this->user = $user;
        $this->username = $username;
        $this->hash = $hash;
        $this->session_key = $encodedSessionKey;
    }

    public function getUser(): UserInterface
    {
        return $this->user;
    }

    public function getUsername(): string
    {
        return $this->username;
    }

    public function setUsername(string $usernameOrEmail): void
    {
        $this->username = $usernameOrEmail;
    }

    public function getHash(): string
    {
        return $this->hash;
    }

    public function setHash(string $hash): void
    {
        $this->hash = $hash;
    }

    public function getSessionKey(): string
    {
        return $this->session_key;
    }

    public function getRawSessionKey(): string
    {
        return base64_decode($this->session_key);
    }

    /**
     * Value gets Base64-encoded for storage
     */
    public function setSessionKey(string $sessionKey): void
    {
        $this->session_key = $sessionKey;
    }

    /**
     * Instead of setting a base64-encoded string, you can set the raw bytes for the key.
     * This setter will base64-encode.
     */
    public function setRawSessionKey(string $sessionKey): void
    {
        $this->session_key = base64_encode($sessionKey);
    }

    public function getResetHash(): string
    {
        return $this->reset_hash;
    }

    public function setResetHash(string $resetHash): void
    {
        $this->reset_hash = $resetHash;
    }

    public function getResetExpiry(): DateTimeImmutable
    {
        return $this->reset_expiry;
    }

    public function setResetExpiry(DateTimeImmutable $dateTime): void
    {
        $this->reset_expiry = $dateTime;
    }

    /**
     * @return mixed
     */
    public function getUserId()
    {
        return $this->getUser()->getId();
    }
}
