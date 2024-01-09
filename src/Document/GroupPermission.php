<?php

declare(strict_types=1);

namespace BplUserMongoDbODM\Document;

use CirclicalUser\Provider\GroupPermissionInterface;
use CirclicalUser\Provider\RoleInterface;
use Doctrine\ODM\MongoDB\Mapping\Annotations as ODM;

use function array_diff;
use function in_array;

/**
 * An example entity that represents an action rule.
 */
#[ODM\Document(collection: "acl_actions")]
class GroupPermission implements GroupPermissionInterface
{
    #[ODM\Id(strategy: "auto")]
    private ?string $id;

    #[ODM\Field(type:"string")]
    private string $resource_class;

    #[ODM\Field(type:"string")]
    private string $resource_id;

    #[ODM\ReferenceOne(targetDocument: Role::class)]
    private RoleInterface $role;

    #[ODM\Field(type:"collection")]
    private array $actions;

    public function __construct(RoleInterface $role, string $resourceClass, string $resourceId, array $actions)
    {
        $this->role = $role;
        $this->resource_class = $resourceClass;
        $this->resource_id = $resourceId;
        $this->actions = $actions;
    }

    public function getResourceClass(): string
    {
        return $this->resource_class;
    }

    public function getResourceId(): string
    {
        return $this->resource_id;
    }

    public function getRole(): RoleInterface
    {
        return $this->role;
    }

    public function getActions(): array
    {
        return $this->actions ?? [];
    }

    public function addAction(string $action): void
    {
        if (!$this->actions) {
            $this->actions = [];
        }

        if (in_array($action, $this->actions, true)) {
            return;
        }

        $this->actions[] = $action;
    }

    public function removeAction(string $action): void
    {
        if (!$this->actions) {
            return;
        }

        $this->actions = array_diff($this->actions, [$action]);
    }

    public function can(string $actionName): bool
    {
        return $this->actions && in_array($actionName, $this->actions, true);
    }
}
