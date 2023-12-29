<?php

namespace BplUserMongoDbODM\Mapper;

use Doctrine\ODM\MongoDB\DocumentManager;
use Doctrine\Persistence\ObjectRepository;
use CirclicalUser\Provider\AuthenticationProviderInterface;
use CirclicalUser\Provider\UserInterface;
use CirclicalUser\Provider\AuthenticationRecordInterface;
use BplUserMongoDbODM\Document\Authentication;
use BplUserMongoDbODM\Document\User;


class AuthenticationMapper implements AuthenticationProviderInterface {
    public function __construct(private DocumentManager $odm) { }

    public function create(UserInterface $user, string $username, string $hash, string $rawKey): AuthenticationRecordInterface {
        return new Authentication($user, $username, $hash, base64_encode($rawKey));
    }

    public function findByUserId($userId): ?AuthenticationRecordInterface {
        return $this->getRepo()->findOneBy([
            'user' => $this->odm->getRepository(User::class)->findOneBy(['id' => $userId])
        ]);
    }

    public function findByUsername(string $username): ?AuthenticationRecordInterface {
        return $this->getRepo()->findOneBy([
            'username' => $username
        ]);
    }

    public function save(object $entity): void {
        $this->odm->persist($entity);
        $this->odm->flush();
    }

    public function update(object $entity): void {
        $this->odm->merge($entity);
        $this->odm->flush();
    }
    
    private function getRepo(): ObjectRepository {
        return $this->odm->getRepository(Authentication::class);
    }
    
    public function delete(object $entity): void {
        $this->odm->remove($entity);
        $this->odm->flush();
    }
}

