<?php

declare(strict_types=1);

namespace BplUserMongoDbODM\Document;

use Doctrine\ODM\MongoDB\Mapping\Annotations as ODM;
use Ramsey\Uuid\UuidInterface;

trait SecretIdPublicUuidTrait
{
    #[ODM\Id(strategy: "increment")]
    #[ODM\Column(type: "integer", options: ["unsigned" => true])]
    #[ODM\GeneratedValue]
    private ?int $id;

    #[ODM\Column(type: "uuid_binary")]
    private UuidInterface $uuid;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUuid(): UuidInterface
    {
        return $this->uuid;
    }
}
