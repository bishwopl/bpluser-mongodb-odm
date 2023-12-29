<?php

namespace BplUserMongoDbODM\Controller\Factory;

use BplAdmin\Controller\UserManagement\RegisterController;
use BplUserMongoDbODM\Document\User;
use BplUserMongoDbODM\Document\Role;
use Interop\Container\ContainerInterface;
use Laminas\ServiceManager\Factory\FactoryInterface;
use DoctrineModule\Form\Element\ObjectSelect;

class BplAdminUserRegisterControllerFactory implements FactoryInterface {

    /**
     * @param ContainerInterface $container
     * @param string $requestedName
     * @param null|array $options
     * @return UserManagementController
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null) {
        $userEntity = User::class;
        $moduleOptions = $container->get(\BplUser\Options\ModuleOptions::class);
        $registrationForm = $container->get($moduleOptions->getRegistrationFormFactory());
        $persistanceManager = $container->get(\Doctrine\ODM\MongoDB\DocumentManager::class);

        if ($moduleOptions->getUseRegistrationFormCaptcha()) {
            $registrationForm->remove('captcha');
        }

        $registrationForm->add([
            'type' => ObjectSelect::class,
            'name' => 'roles',
            'options' => [
                "label" => "Roles",
                'object_manager' => $persistanceManager,
                'target_class' => Role::class,
                'label_generator' => function (Role $role) {
                    return $role->getName();
                },
            ],
            "attributes" => [
                "id" => "roleId",
                'multiple' => true,
                "class" => "form-control input",
            ]
        ]);

        $submitElem = "";
        foreach ($registrationForm as $element) {
            if ($element instanceof \Laminas\Form\Element && $element->getAttribute('type') !== 'submit') {
                $element->setAttribute("class", "form-control input");
            }elseif($element->getAttribute('type') == 'submit'){
                $submitElem = $element;
                $registrationForm->remove($element->getName());
            }
        }
        
        if(is_object($submitElem)){
            $registrationForm->add($submitElem);
        }
        
        return new RegisterController(
                $container->get(\BplUser\Service\BplUserService::class),
                $container->get(\CirclicalUser\Service\AuthenticationService::class),
                $registrationForm,
                new $userEntity
        );
    }

}
