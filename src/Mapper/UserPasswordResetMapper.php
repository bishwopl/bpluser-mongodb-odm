<?php

namespace BplUserMongoDbODM\Mapper;

use BplUser\Contract\UserPasswordResetMapperInterface;
use BplUser\Contract\UserPasswordResetInterface;
use BplUserMongoDbODM\Document\UserPasswordReset;
use Doctrine\ODM\MongoDB\DocumentManager;
use Doctrine\Persistence\ObjectRepository;
use MongoDB\BSON\ObjectId;

class UserPasswordResetMapper implements UserPasswordResetMapperInterface {

    public function __construct(private DocumentManager $odm) { }

    public function findByRequestKey($requestKey): UserPasswordResetInterface {
        return $this->getRepo()->findOneBy([
            'requestKey' => $requestKey
        ]);
    }

    public function getClassName() {
        return UserPasswordReset::class;
    }

    public function getEntity(): UserPasswordResetInterface {
        return new UserPasswordReset();
    }

    public function getResetRecordByUserIdAndToken(string|int $userId, string $token): ?UserPasswordResetInterface {
        $resetRecord = $this->getRepo()->findOneBy([
            'requestKey' => $token
        ]);
        
        if($resetRecord instanceof UserPasswordReset && $resetRecord->getUser()->getId() == $userId){
            return $resetRecord;
        }
        return null;
    }

    public function removeByRequestKey($requestKey) {
        $doc = $this->getRepo()->findOneBy([
            'requestKey' => $requestKey
        ]);
        
        if(is_object($doc)){
            $this->odm->remove($doc);
            $this->odm->flush();
        }
    }

    public function removeByUserId($userId) {
        $doc = $this->getRepo()->findOneBy([
            'user' => new ObjectId($userId),
        ]);
        
        if(is_object($doc)){
            $this->odm->remove($doc);
            $this->odm->flush();
        }
    }

    public function removeOlderRequests(int $expireTime) {
        $now = new \DateTime((int)$expireTime . ' seconds ago');
        $this->odm->createQueryBuilder()->findAndRemove(UserPasswordReset::class)->field('requestTime')->lt($now);
    }

    public function savePasswordResetRequestRecord(UserPasswordResetInterface $request): UserPasswordResetInterface {
        $this->odm->persist($request);
        $this->odm->flush();
        return $request;
    }
    
    private function getRepo(): ObjectRepository {
        return $this->odm->getRepository(UserPasswordReset::class);
    }
}
