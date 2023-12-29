<?php

namespace BplUserMongoDbODM\Mapper\Factory;

use Laminas\ServiceManager\Factory\FactoryInterface;
use Psr\Container\ContainerInterface;
use Doctrine\ODM\MongoDB\DocumentManager;
use BplUserMongoDbODM\Mapper\UserMapper;

class UserMapperFactory implements FactoryInterface {

    public function __invoke(ContainerInterface $container, $requestedName, Array $options = null) {
        $odm = $container->get(DocumentManager::class);
        return new UserMapper($odm);
    }

}
