<?php

namespace BplUserMongoDbODM\Form\Factory;

use Laminas\ServiceManager\Factory\FactoryInterface;
use Psr\Container\ContainerInterface;
use Doctrine\Laminas\Hydrator\DoctrineObject as DoctrineHydrator;
use BplUser\Form\ChangeProfile;
use BplUser\Form\Filter\ChangeProfileFilter;
use Laminas\Form\FormElementManager;
use Doctrine\ODM\MongoDB\DocumentManager;

class ChangeProfileFormFactory implements FactoryInterface{
    /**
     * 
     * @param ContainerInterface $container
     * @param type $requestedName
     * @param array $options
     * @return \BplUser\Form\ChangeProfile
     */
    public function __invoke(ContainerInterface $container, $requestedName, Array $options = null) {
        $em = $container->get(DocumentManager::class);
        
        $formManager = $container->get(FormElementManager::class);
        $changeProfileForm = $formManager->get(ChangeProfile::class);
        $changeProfileForm->setName('change-profile-form');
        $changeProfileForm->setInputFilter(new ChangeProfileFilter());
        $changeProfileForm->setHydrator(new DoctrineHydrator($em));
        return $changeProfileForm;
    }
}