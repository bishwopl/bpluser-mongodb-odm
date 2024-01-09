<?php

namespace BplUserMongoDbODM\Mapper;

use BplUserMongoDbODM\Document\GroupPermission;
use CirclicalUser\Provider\GroupPermissionProviderInterface;
use CirclicalUser\Provider\GroupPermissionInterface;
use CirclicalUser\Provider\RoleInterface;
use CirclicalUser\Provider\ResourceInterface;
use Doctrine\ODM\MongoDB\DocumentManager;

class GroupPermissionMapper implements GroupPermissionProviderInterface {

    public function __construct(private DocumentManager $odm) {
        
    }

    public function create(RoleInterface $role, string $resourceClass, string $resourceId, array $actions): GroupPermissionInterface {

        $oldAcl = $this->odm->getRepository(GroupPermission::class)->findBy([
            'role' => $role,
            'resource_class' => $resourceClass,
            'resource_id' => $resourceId
        ]);
        if (!$oldAcl instanceof GroupPermission) {
            $newAcl = new GroupPermission($role, $resourceClass, $resourceId, $actions);
            $this->save($newAcl);
            return $newAcl;
        }
        $oldActions = $oldAcl->getActions();
        foreach ($oldActions as $a) {
            if (!in_array($a, $actions)) {
                $oldAcl->removeAction($a);
            }
        }
        foreach ($actions as $a) {
            if (!in_array($a, $oldActions)) {
                $oldAcl->addAction($a);
            }
        }
        $this->update($oldAcl);

        return $oldAcl;
    }

    public function getPermissions(string $string): array {
        return $this->odm->getRepository(GroupPermission::class)->findBy([
                    'resource_class' => $string,
                    'resource_id' => $string
        ]);
    }

    public function getResourcePermissions(ResourceInterface $resource): array {
        return $this->odm->getRepository(GroupPermission::class)->findBy([
                    'resource_class' => $resource->getClass(),
                    'resource_id' => $resource->getId()
        ]);
    }

    public function getResourcePermissionsByClass(string $resourceClass): array {
        return $this->odm->getRepository(GroupPermission::class)->findBy([
                    'resource_class' => $resourceClass
        ]);
    }

    public function save(object $entity) {
        $this->odm->persist($entity);
        $this->odm->flush();
    }

    public function update(object $entity) {
        $this->odm->merge($entity);
        $this->odm->flush();
    }

    public function delete(object $entity) {
        $this->odm->remove($entity);
        $this->odm->flush();
    }
}
