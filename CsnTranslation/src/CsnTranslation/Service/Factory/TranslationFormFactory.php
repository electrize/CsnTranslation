<?php
/**
 * CsnUser - Coolcsn Zend Framework 2 User Module
 *
 * @link https://github.com/coolcsn/CsnUser for the canonical source repository
 * @copyright Copyright (c) 2005-2013 LightSoft 2005 Ltd. Bulgaria
 * @license https://github.com/coolcsn/CsnUser/blob/master/LICENSE BSDLicense
 * @author Stoyan Cheresharov <stoyan@coolcsn.com>
 * @author Svetoslav Chonkov <svetoslav.chonkov@gmail.com>
 * @author Nikola Vasilev <niko7vasilev@gmail.com>
 * @author Stoyan Revov <st.revov@gmail.com>
 * @author Martin Briglia <martin@mgscreativa.com>
 */

namespace CsnTranslation\Service\Factory;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

use DoctrineORMModule\Form\Annotation\AnnotationBuilder as DoctrineAnnotationBuilder;
use DoctrineModule\Stdlib\Hydrator\DoctrineObject as DoctrineHydrator;
use DoctrineModule\Validator\NoObjectExists as NoObjectExistsValidator;

use HotelRome\Entity\Apartment;

class TranslationFormFactory implements FactoryInterface
{

    /**
     * @var Zend\Form\Form
     */
    private $form;

    /**
     * @var ServiceLocatorInterface
     */
    private $serviceLocator;

    /**
     * @var ModuleOptions
     */
    protected $options;

    /**
     * @var Doctrine\ORM\EntityManager
     */
    protected $entityManager;

    /**
     * @var Zend\Mvc\I18n\Translator
     */
    protected $translatorHelper;

    /**
     * @var Zend\Mvc\Controller\Plugin\Url
     */
    protected $url;


    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $this->serviceLocator = $serviceLocator;
        return $this;
    }

    /**
     * Create form
     *
     * Method to create the Doctrine ORM forms
     *
     * @return Zend\Form\Form
     */
    public function createForm($entity, $formName)
    {
        $builder = new DoctrineAnnotationBuilder($this->getEntityManager());
        $this->form = $builder->createForm($entity);
        $this->form->setHydrator(new DoctrineHydrator($this->getEntityManager()));
        $this->form->setAttribute('method', 'post');

        $this->addCommonFields();

        switch($formName) {
          case 'Add':
                break;
          case 'Edit':

              break;
        }

        $this->form->bind($entity);

        return $this->form;
    }

    /**
     *
     * Common Fields
     *
     */
    private function addCommonFields()
    {
        $this->form->add(array(
            'name' => 'csrf',
            'type' => 'Zend\Form\Element\Csrf',
            'options' => array(
                'csrf_options' => array(
                    'timeout' => 2592000, // one month
                )
            )
        ));

        $this->form->add(array(
            'name' => 'submit',
            'type' => 'Zend\Form\Element\Submit',
            'attributes' => array(
                'type'  => 'submit',
				'value' => 'Submit'
            ),
        ));
    }

    /**
     * get options
     *
     * @return ModuleOptions
     */
    private function getOptions()
    {
        if(null === $this->options) {
            $this->options = $this->serviceLocator->get('csnTranslationModuleOptions');
        }

        return $this->options;
    }

    /**
     * get entityManager
     *
     * @return Doctrine\ORM\EntityManager
     */
    private function getEntityManager()
    {
        if(null === $this->entityManager) {
            $this->entityManager = $this->serviceLocator->get('doctrine.entitymanager.orm_default');
        }

        return $this->entityManager;
    }

    /**
     * get translatorHelper
     *
     * @return  Zend\Mvc\I18n\Translator
     */
    private function getTranslatorHelper()
    {
        if(null === $this->translatorHelper) {
            $this->translatorHelper = $this->serviceLocator->get('MvcTranslator');
        }

        return $this->translatorHelper;
    }

    /**
     * get urlPlugin
     *
     * @return  Zend\Mvc\Controller\Plugin\Url
     */
    private function getUrlPlugin()
    {
        if(null === $this->url) {
            $this->url = $this->serviceLocator->get('ControllerPluginManager')->get('url');
        }

        return $this->url;
    }
}
