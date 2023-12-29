<?php

declare(strict_types=1);

namespace BplUserMongoDbODM\Document;

use CirclicalUser\Provider\UserInterface;
use DateTimeImmutable;
use Doctrine\ODM\MongoDB\Mapping\Annotations as ODM;

/**
 * Log entity that you can use when users log in.
 */
#[ODM\Document(collection: "users_auth_logs")]
#[ODM\Index(keys: ["auth_time"=>"asc", "ip_address"=>"asc"])]
class UserAuthenticationLog {

    #[ODM\Id(strategy: "auto")]
    private $id;

    #[ODM\ReferenceOne(targetDocument: "BplUserMongoDbODM\Document\User", inversedBy: "authenticationLogs")]
    private UserInterface $user;

    #[ODM\Field(type: "date_immutable")]
    private DateTimeImmutable $authTime;

    #[ODM\Field(type: "string", nullable: true, options: ["fixed" => true])]
    private ?string $ipAddress;

    public function __construct(UserInterface $user, DateTimeImmutable $authTime, ?string $ipAddress) {
        $this->id = 0;
        $this->user = $user;
        $this->authTime = $authTime;
        $this->ipAddress = $ipAddress;
    }

    public function getId(): int {
        return $this->id;
    }

    public function getUser(): UserInterface {
        return $this->user;
    }

    public function getAuthTime(): DateTimeImmutable {
        return $this->authTime;
    }

    public function getIpAddress(): ?string {
        return $this->ipAddress;
    }
}
