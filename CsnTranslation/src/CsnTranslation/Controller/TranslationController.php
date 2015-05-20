<?php

namespace CsnTranslation\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use CsnTranslation\Entity\Translation;

// For paginator
use DoctrineORMModule\Paginator\Adapter\DoctrinePaginator as DoctrineAdapter;
use Doctrine\ORM\Tools\Pagination\Paginator as ORMPaginator;
use Zend\Paginator\Paginator as ZendPaginator;

/**
 * Index controller
 */
class TranslationController extends AbstractActionController
{

    /**
     * @var CsnUser ModuleOptions
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
     * @var Zend\Form\Form
     */
    protected $formBuilder;

    // R - retrieve
    public function indexAction()
    {
        if (!$this->identity()) {
            return $this->redirect()->toRoute('h-rome');
        }

        $dql = "SELECT t FROM CsnTranslation\Entity\Translation t ORDER BY t.id DESC";
        $query = $this->getEntityManager()->createQuery($dql);

        $adapter = new DoctrineAdapter(new ORMPaginator($query));
        $paginator = new ZendPaginator($adapter);
        $paginator->setDefaultItemCountPerPage(8);
        $paginator->setPageRange(7); // Default is 10

        $page = (int) $this->params()->fromRoute('page');
        if ($page) {
            $paginator->setCurrentPageNumber($page);
        }

        return array('translations' => $paginator);
    }

    public function searchAction()
    {
        if (!$this->identity()) {
            return $this->redirect()->toRoute('h-rome');
        }

        $request = $this->getRequest();

        if ($request->isPost()) {
            $post = $request->getPost();
            if (empty($post['filter'])) {
                return $this->redirect()->toRoute('admin-translation');
            }
            $param = str_replace('/', '-', $post['filter']);
            $this->redirect()->toRoute('admin-translation', array('action' => 'search', 'filter' => $param));
        }

        $filter = $this->params()->fromRoute('filter');
        $param = str_replace('-', '/', $filter);

        $filter = '%'.strtolower($param).'%';

        $dql = "SELECT t, l FROM 'CsnTranslation\Entity\Translation' t
                LEFT JOIN t.locale l WITH l.id = t.locale
                WHERE (
                    LOWER(t.token) LIKE :filter OR
                    LOWER(t.translation) LIKE :filter OR
                    LOWER(l.name) LIKE :filter
                    )
                ORDER BY t.id DESC";

        $query = $this->getEntityManager()->createQuery($dql);
        $query->setParameters(array('filter' => $filter));

        $adapter = new DoctrineAdapter(new ORMPaginator($query));
        $paginator = new ZendPaginator($adapter);
        $paginator->setDefaultItemCountPerPage(8);
        $paginator->setPageRange(7); // Default is 10

        $page = (int) $this->params()->fromRoute('page');
        if ($page) {
            $paginator->setCurrentPageNumber($page);
        }

        return array('translations' => $paginator);

        //return $this->redirect()->toRoute('admin-dashboard-photo');
    }

    // C - create
    public function addAction()
    {
        if (!$this->identity()) {
            return $this->redirect()->toRoute('h-rome');
        }

        $translation = new Translation();

        $form = $this->getFormBuilder()->createForm($translation, 'Add');

        $request = $this->getRequest();

        if ($request->isPost()) {
            $response   = $this->getResponse();

            $post = $request->getPost();
            $form->setValidationGroup('locale', 'token', 'translation', 'csrf');

            $form->setData($post);
            $domain = 'hotel';
            //Check if translation exists
            $recordExist = $this->getEntityManager()->getRepository('CsnTranslation\Entity\Translation')->findOneBy(array('locale' => $post['locale'], 'domain' => $domain, 'token' => $post['token']));
            if (isset($recordExist)) {
                $message = sprintf($this->getTranslatorHelper()->translate("Translation for %s already exist", 'csntranslation'), '"'.$post['token'].'"');

                return array('form' => $form, 'message' => $this->getTranslatorHelper()->translate($message, 'csntranslation'));
            }

            if ($form->isValid()) {
                $translation->setDomain($domain);
                $this->getEntityManager()->persist($translation);
                $this->getEntityManager()->flush();

                return $this->redirect()->toRoute('admin-translation', array('controller' => 'translation', 'action' => 'index'));
            }
        }

        return array('form' => $form);
    }

    // U - update
    public function editAction()
    {
        if (!$this->identity()) {
            return $this->redirect()->toRoute('h-rome');
        }

        $id = $this->params()->fromRoute('id');
        if (!$id) {
            return $this->redirect()->toRoute('admin-translation', array('controller' => 'translation', 'action' => 'index'));
        }

        $translation = $this->getEntityManager()->find('CsnTranslation\Entity\Translation', $id);

        if (!$translation) {
            return $this->redirect()->toRoute('admin-translation', array('controller' => 'translation', 'action' => 'index'));
        }

        $form = $this->getFormBuilder()->createForm($translation, 'Edit');

        $request = $this->getRequest();

        if ($request->isPost()) {
            $post = $request->getPost();
            $form->setValidationGroup('locale', 'token', 'translation', 'csrf');
            $form->setData($post);
            $domain = 'hotel';

            //Check if translation exists
            $recordExist = $this->getEntityManager()->getRepository('CsnTranslation\Entity\Translation')->findOneBy(array('locale' => $post['locale'], 'domain' => $domain, 'token' => $post['token']));
            if (isset($recordExist) && $recordExist->getId() != $id) {
                $message = sprintf($this->getTranslatorHelper()->translate("Translation for %s already exist", 'csntranslation'), '"'.$post['token'].'"');

                return array('form' => $form, 'id' => $id, 'message' => $this->getTranslatorHelper()->translate($message, 'csntranslation'));
            }

            if ($form->isValid()) {
                $translation->setDomain($domain);
                $this->getEntityManager()->persist($translation);
                $this->getEntityManager()->flush();

                return $this->redirect()->toRoute('admin-translation', array('controller' => 'translation', 'action' => 'index'));
            }
        }

        return array('form' => $form, 'id' => $id);
    }

    // D - delete
    public function deleteAction()
    {
        if (!$this->identity()) {
            return $this->redirect()->toRoute('h-rome');
        }
        $id = $this->params()->fromRoute('id');
        if (!$id) {
            return $this->redirect()->toRoute('admin-translation', array('controller' => 'translation', 'action' => 'index'));
        }

        $translation = $this->getEntityManager()->find('CsnTranslation\Entity\Translation', $id);

        if ($translation) {
            //Delete the article
            $this->getEntityManager()->remove($translation);
            $this->getEntityManager()->flush();
        }

        return $this->redirect()->toRoute('admin-translation', array('controller' => 'translation', 'action' => 'index'));
    }

    /**
     * get entityManager
     *
     * @return EntityManager
     */
    private function getEntityManager()
    {
        if (null === $this->entityManager) {
            $this->entityManager = $this->getServiceLocator()->get('doctrine.entitymanager.orm_default');
        }

        return $this->entityManager;
    }

    /**
     * get formBuilder
     *
     * @return Zend\Form\Form
     */
    private function getFormBuilder()
    {
        if (null === $this->formBuilder) {
            $this->formBuilder = $this->getServiceLocator()->get('csnTranslationFormBuilder');
        }

        return $this->formBuilder;
    }

    /**
     * get translatorHelper
     *
     * @return Zend\Mvc\I18n\Translator
     */
    private function getTranslatorHelper()
    {
        if (null === $this->translatorHelper) {
            $this->translatorHelper = $this->getServiceLocator()->get('MvcTranslator');
        }

        return $this->translatorHelper;
    }

    /**
     * get options
     *
     * @return ModuleOptions
     */
    private function getOptions()
    {
        if (null === $this->options) {
            $this->options = $this->getServiceLocator()->get('csnTranslationModuleOptions');
        }

        return $this->options;
    }
}
