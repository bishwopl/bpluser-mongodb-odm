<?php

namespace BplUserMongoDbODM\Controller\Factory;

use BplUserMongoDbODM\Document\User;
use Laminas\ServiceManager\Factory\FactoryInterface;
use Psr\Container\ContainerInterface;

class RegisterControllerFactory implements FactoryInterface {

    /**
     * 
     * @param ContainerInterface $container
     * @param type $requestedName
     * @param array $options
     * @return BplUserController
     */
    public function __invoke(ContainerInterface $container, $requestedName, Array $options = null) {
        $userEntity = User::class;
        $bplUserService = $container->get(\BplUser\Service\BplUserService::class);
        $moduleOptions = $container->get(\BplUser\Options\ModuleOptions::class);
        $registrationForm = $container->get($moduleOptions->getRegistrationFormFactory());
        return new \BplUser\Controller\RegisterController(
                $moduleOptions, $bplUserService, $registrationForm, new $userEntity
        );
    }

}
