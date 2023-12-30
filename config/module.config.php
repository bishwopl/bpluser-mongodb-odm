<?php

namespace BplUserMongoDbODM;

return [
    'controllers' => [
        'factories' => [
            \BplUser\Controller\RegisterController::class => Controller\Factory\RegisterControllerFactory::class,
            \BplUser\Controller\Factory\ChangeProfileControllerFactory::class => Controller\Factory\ChangeProfileControllerFactory::class,
            \BplAdmin\Controller\UserManagement\RegisterController::class => Controller\Factory\BplAdminUserRegisterControllerFactory::class,
        ],
    ],
    'circlical' => [
        'user' => [
            'providers' => [
                'auth' => Mapper\AuthenticationMapper::class,
                'user' => Mapper\UserMapper::class,
                'role' => Mapper\RoleMapper::class,
                'rules' => [
                    'group' => Mapper\GroupPermissionMapper::class,
                    'user' => Mapper\UserPermissionMapper::class,
                ],
            ],
        ],
    ],
    'service_manager' => [
        'factories' => [
            Mapper\AuthenticationMapper::class => Mapper\Factory\AuthenticationMapperFactory::class,
            Mapper\UserMapper::class => Mapper\Factory\UserMapperFactory::class,
            Mapper\RoleMapper::class => Mapper\Factory\RoleMapperFactory::class,
            Mapper\GroupPermissionMapper::class => Mapper\Factory\GroupPermissionMapperFactory::class,
            Mapper\UserPermissionMapper::class => Mapper\Factory\UserPermissionMapperFactory::class,
            \BplUser\Mapper\UserPasswordResetMapper::class => Mapper\Factory\UserPasswordResetMapperFactory::class,
            \BplUser\Form\ChangeProfile::class => Form\Factory\ChangeProfileFormFactory::class,
            \BplUser\Form\Register::class => Form\Factory\RegisterFormFactory::class,
            \CirclicalUser\Service\AuthenticationService::class => Service\AuthenticationServiceFactory::class,
        ],
    ],
    'doctrine' => [
        'driver' => [
            'odm_default' => [
                'drivers' => [
                    'BplUserMongoDbODM\Document' => __NAMESPACE__ . '_driver'
                ],
            ],
            __NAMESPACE__ . '_driver' => [
                'class' => \Doctrine\ODM\MongoDB\Mapping\Driver\AttributeDriver::class,
                'cache' => 'array',
                'paths' => [
                    __DIR__ . '/../src/Document',
                ],
            ],
        ],
    ],
];

