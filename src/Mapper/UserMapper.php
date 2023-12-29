<?php

namespace BplUserMongoDbODM\Mapper;

use Doctrine\ODM\MongoDB\DocumentManager;
use Doctrine\Persistence\ObjectRepository;
use CirclicalUser\Provider\UserProviderInterface;
use CirclicalUser\Provider\UserInterface;
use BplUserMongoDbODM\Document\User;

class UserMapper implements UserProviderInterface {

    public function __construct(private DocumentManager $odm) { }

    public function findByEmail(string $email): ?UserInterface {
        return $this->getRepo()->findOneBy(['email' => $email]);
    }

    public function getUser($userId): ?UserInterface {
        return $this->getRepo()->findOneBy(['id' => $userId]);
    }

    public function save(object $entity): void {
        $this->odm->persist($entity);
        $this->odm->flush();
    }

    public function update(object $entity): void {
        $this->odm->merge($entity);
        $this->odm->flush();
    }
    
    public function getAllUsers(){
        return $this->getRepo()->findAll();
    }

    private function getRepo(): ObjectRepository {
        return $this->odm->getRepository(User::class);
    }
    public function delete(object $entity): void {
        $this->odm->remove($entity);
        $this->odm->flush();
    }
}
