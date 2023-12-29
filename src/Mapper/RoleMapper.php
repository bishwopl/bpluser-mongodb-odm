<?php

namespace BplUserMongoDbODM\Mapper;

use CirclicalUser\Provider\RoleProviderInterface;
use Doctrine\ODM\MongoDB\DocumentManager;
use BplUserMongoDbODM\Document\Role;
use CirclicalUser\Provider\RoleInterface;

class RoleMapper implements RoleProviderInterface {

    public function __construct(private DocumentManager $odm) {
        
    }

    public function getAllRoles(): array {
        return $this->odm->getRepository(Role::class)->findAll();
    }

    public function getRoleWithName(string $name): ?\CirclicalUser\Provider\RoleInterface {
        return $this->odm->getRepository(Role::class)->findOneBy([
                    'name' => $name
        ]);
    }
    
    public function save(RoleInterface $role){
        $role->setId($this->getNextRoleId());
        $this->odm->persist($role);
        $this->odm->flush();
    }
    
    public function update(object $entity): void {
        $this->odm->merge($entity);
        $this->odm->flush();
    }

    public function delete(object $entity): void {
        $this->odm->remove($entity);
        $this->odm->flush();
    }
    
    private function getNextRoleId(){
        $ret = 1;
        $role = $this->odm->createQueryBuilder(Role::class)->sort('roleId','desc')->getQuery()->getSingleResult();
        if($role instanceof Role){
            $ret = $role->getId()+1;
        }
        return $ret;
    }
}
