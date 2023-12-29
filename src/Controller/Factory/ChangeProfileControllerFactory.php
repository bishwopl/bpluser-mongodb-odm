<?php

namespace BplUserMongoDbODM\Controller\Factory;

use BplUser\Controller\ChangeProfileController;
use BplUserMongoDbODM\Document\User;
use Laminas\ServiceManager\Factory\FactoryInterface;
use Psr\Container\ContainerInterface;

class ChangeProfileControllerFactory implements FactoryInterface{
    public function __invoke(ContainerInterface $container, $requestedName, Array $options = null) {
        $moduleOptions = $container->get(\BplUser\Options\ModuleOptions::class);
        $userEntity = User::class;
        $changeProfileForm = $container->get($moduleOptions->getChangeProfileFormFactory());
        return new ChangeProfileController($moduleOptions, $changeProfileForm, new $userEntity);
    }
}