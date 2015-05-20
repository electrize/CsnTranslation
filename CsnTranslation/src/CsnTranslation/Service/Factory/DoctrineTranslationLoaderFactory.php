<?php

namespace CsnTranslation\Service\Factory;

/**
 * CsnTranslation - Coolcsn Zend Framework 2 User Module
 * 
 * @link https://github.com/coolcsn/CsnTranslation for the canonical source repository
 * @copyright Copyright (c) 2005-2013 LightSoft 2005 Ltd. Bulgaria
 * @license https://github.com/coolcsn/CsnTranslation/blob/master/LICENSE BSDLicense
 * @author Stoyan Cheresharov <stoyan@coolcsn.com>
 * @author Svetoslav Chonkov <svetoslav.chonkov@gmail.com>
 * @author Nikola Vasilev <niko7vasilev@gmail.com>
 */

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

use CsnTranslation\I18n\Translator\Loader\DoctrineTranslationLoader;

class DoctrineTranslationLoaderFactory implements FactoryInterface
{
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
    	$sm = $serviceLocator->getServiceLocator();
       	$em = $sm->get('Doctrine\ORM\EntityManager');
        return new DoctrineTranslationLoader($em);
    }
}
