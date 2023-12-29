<?php

declare(strict_types=1);

namespace BplUserMongoDbODM\Document;

use CirclicalUser\Provider\RoleInterface;
use Doctrine\ODM\MongoDB\Mapping\Annotations as ODM;
use Doctrine\ODM\MongoDB\Types\Type;

/**
 * An example entity that represents a role.
 */
#[ODM\Document(collection: "acl_roles")]
#[ODM\Index(keys: ["name" => "desc"], options: ["unique" => true])]
#[ODM\Index(keys: ["name" => "roleId"], options: ["unique" => true])]
class Role implements RoleInterface
{
    #[ODM\Id(strategy: "auto")]
    private ?string $id;
    
    #[ODM\Field(type: Type::INT, nullable: true)]
    private ?int $roleId = null;

    #[ODM\Field(type: "string", nullable: true)]
    private string $name;

    #[ODM\ReferenceOne(targetDocument: Role::class)]
    private ?RoleInterface $parent = null;

    public function __construct(string $name, ?RoleInterface $parent)
    {
        $this->id = null;
        $this->name = $name;
        $this->parent = $parent;
    }

    public function getId(): int
    {
        return $this->getRoleId();
    }

    /**
     * Probably shouldn't be used, but in case some folks have weird edge conditions, I'll leave it.
     */
    public function setId(int $id): void
    {
        $this->roleId = $id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): void
    {
        $this->name = $name;
    }

    /**
     * Get the parent role
     */
    public function getParent(): ?RoleInterface
    {
        return $this->parent;
    }

    /**
     * Set the parent role.
     */
    public function setParent(?RoleInterface $parent): void
    {
        $this->parent = $parent;
    }

    /**
     * Return all inherited roles, including the start role, in this to-root traversal.
     */
    public function getInheritanceList(): array
    {
        $roleList = [$this];
        $role = $this;
        while ($parentRole = $role->getParent()) {
            $roleList[] = $parentRole;
            $role = $parentRole;
        }

        return $roleList;
    }
    
    public function getRoleId(): ?int {
        return $this->roleId;
    }

    public function setRoleId(?int $roleId): void {
        $this->roleId = $roleId;
    }


}
