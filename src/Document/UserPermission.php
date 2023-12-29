<?php

declare(strict_types=1);

namespace BplUserMongoDbODM\Document;

use CirclicalUser\Provider\UserInterface;
use CirclicalUser\Provider\UserPermissionInterface;
use Doctrine\ODM\MongoDB\Mapping\Annotations as ODM;

/**
 * Similar to a standard action rule, that is role-based -- this one is user-based.
 * Used in cases where roles don't fit.
 */
#[ODM\Document(collection: "acl_actions_users")]
#[ODM\Index(keys : ['user.$id'=> 1, "resource_class"=> 1, "resource_id"=> 1], options : ["unique"=>true])]
class UserPermission implements UserPermissionInterface
{
    #[ODM\Id]
    protected int $id;

    #[ODM\Field(type:"string")]
    protected string $resource_class;

    #[ODM\Field(type:"string")]
    protected string $resource_id;

    #[ODM\ReferenceOne(targetDocument:"\BplUserMongoDbODM\Document\User", inversedBy:"userPermissions")]
    protected UserInterface $user;

    #[ODM\Field(type:"hash")]
    protected array $actions;

    public function __construct(UserInterface $user, string $resourceClass, string $resourceId, array $actions)
    {
        $this->user = $user;
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

    public function getUser(): UserInterface
    {
        return $this->user;
    }

    public function getActions(): array
    {
        if (!$this->actions) {
            return [];
        }

        return $this->actions;
    }

    public function addAction(string $action): void
    {
        if (!$this->actions) {
            $this->actions = [];
        }
        if (!in_array($action, $this->actions, true)) {
            $this->actions[] = $action;
        }
    }

    public function removeAction(string $action): void
    {
        if ($this->actions) {
            $this->actions = array_diff($this->actions, [$action]);
        }
    }

    public function can(string $actionName): bool
    {
        return in_array($actionName, $this->actions, true);
    }
}
