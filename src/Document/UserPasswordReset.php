<?php

namespace BplUserMongoDbODM\Document;

use BplUser\Contract\UserPasswordResetInterface;
use BplUserMongoDbODM\Document\User;
use Doctrine\ODM\MongoDB\Mapping\Annotations as ODM;

#[ODM\Document(collection: "user_password_reset")]
#[ODM\Index(keys: ['user.$id' => "desc"], options: ["unique" => true])]
class UserPasswordReset implements UserPasswordResetInterface
{
    #[ODM\Id(strategy: "auto")]
    private ?string $id = null;
    
    #[ODM\Field(type:"string")]
    private string $requestKey;

    #[ODM\Field(type:"date")]
    private \DateTime $requestTime;

    #[ODM\ReferenceOne(targetDocument:User::class, inversedBy:"userPasswordReset")]
    private \BplUser\Contract\BplUserInterface $user;

    /**
     * Set requestKey
     *
     * @param string $requestKey
     *
     * @return UserPasswordReset
     */
    public function setRequestKey(string $requestKey): self
    {
        $this->requestKey = $requestKey;
        return $this;
    }

    /**
     * Get requestKey
     *
     * @return string
     */
    public function getRequestKey(): string
    {
        return $this->requestKey;
    }

    /**
     * Set requestTime
     *
     * @param \DateTime $requestTime
     *
     * @return UserPasswordReset
     */
    public function setRequestTime(\DateTimeInterface $requestTime): self
    {
        $this->requestTime = $requestTime;
        return $this;
    }

    /**
     * Get requestTime
     *
     * @return \DateTime
     */
    public function getRequestTime(): \DateTimeInterface
    {
        return $this->requestTime;
    }

    /**
     * Set user
     *
     * @param \BplUser\Contract\BplUserInterface|null $user
     *
     * @return UserPasswordReset
     */
    public function setUser(\BplUser\Contract\BplUserInterface $user): self
    {
        $this->user = $user;
        return $this;
    }

    /**
     * Get user
     *
     * @return \BplUser\Contract\BplUserInterface|null
     */
    public function getUser(): \BplUser\Contract\BplUserInterface
    {
        return $this->user;
    }
    
    public function getId() {
        return $this->id;
    }

}
