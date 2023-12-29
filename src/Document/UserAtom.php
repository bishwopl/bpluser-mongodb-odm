<?php

declare(strict_types=1);

namespace BplUserMongoDbODM\Document;

use CirclicalUser\Provider\UserInterface;
use Doctrine\ODM\MongoDB\Mapping\Annotations as ODM;

#[ODM\Document(collection: "users_atoms")]
#[ODM\Index(keys: ["key" => "asc", "value" => "asc"], options: ["unique" => true])]
class UserAtom {
    
    #[ODM\Id(strategy: "auto")]
    private $id;

    #[ODM\ReferenceOne(targetDocument: "BplUserMongoDbODM\Document\User", inversedBy: "userAtoms")]
    private UserInterface $user;

    #[ODM\Id]
    #[ODM\Field(type: "string", name: "`key`")]
    private string $key;

    #[ODM\Field(type: "string", name: "`value`")]
    private string $value;

    public function __construct(UserInterface $user, string $key, string $value) {
        $this->user = $user;
        $this->key = $key;
        $this->value = $value;
    }

    public function getUser(): UserInterface {
        return $this->user;
    }

    public function getKey(): string {
        return $this->key;
    }

    public function getValue(): string {
        return $this->value;
    }

    public function setValue(string $value): void {
        $this->value = $value;
    }
}
