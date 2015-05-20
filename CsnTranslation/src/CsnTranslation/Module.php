<?php

namespace CsnTranslation;

class Module
{

    private function setCookie($responseHeaders, $name, $value, $expires = null, $path = null, $domain = null, $secure = false, $httponly = false, $maxAge = null, $version = null)
    {
        $cookie = new \Zend\Http\Header\SetCookie($name, $value, $expires, $path, $domain, $secure, $httponly, $maxAge, $version);
        $responseHeaders->addHeader($cookie);
    }

    private function getLocaleFromRequest($requestHeaders, $responseHeaders, $entityManager, $defaultLocale)
    {
        /*$dql = "SELECT l.locale FROM CsnTranslation\Entity\Locale l ORDER BY l.locale ASC";
        $query = $entityManager->createQuery($dql);
        $possibleLocales = $query->getResult(); // \Doctrine\ORM\Query::HYDRATE_ARRAY
        
        $possibleLocales = array_column($possibleLocales, 'locale');
        */
        $possibleLocales = array(); // should be manually written to boost the performance
        //$possibleLocales = array('en_US', 'en_AU','bg_BG', 'ru_RU', 'it_IT', 'ja_JP', 'ps_AF', 'gd_GD', 'ar_AR', 'en_IN');


        // Get locale from "Cookie". Executed on every request.
        $hasCookie = false;
        if ($requestHeaders->has('Cookie')) {
            $hasCookie = true;
            $cookies = $requestHeaders->get('Cookie');
            if ($cookies->offsetExists('locale') && in_array($cookies->offsetGet('locale'), $possibleLocales)) {
                return $cookies->offsetGet('locale');
            }
        }

        // Get locale from "Accept-Language". Executed only on first request OR if not set cookie "locale".
        if ($requestHeaders->has('Accept-Language') && !$hasCookie) {
            $headerLocales = $requestHeaders->get('Accept-Language')->getPrioritized();
            foreach ($headerLocales as $locale) {
                $locale = str_replace('-', '_', $locale->getLanguage());
                //\Zend\Debug\Debug::Dump($locale);
                if (!empty($matches = preg_grep('/^'.$locale.'$/i', $possibleLocales))) {
                    // whole e.g. if $locale = "ja_JP"
                    //\Zend\Debug\Debug::Dump($matches);
                    $locale = reset($matches);
                    $this->setCookie($responseHeaders, 'locale', $locale, time() + 365 * 60 * 60 * 24, '/');  // now + 1 year
                    return $locale;
                }
                if (strlen($locale) <= 3 && !stripos($locale, '_')) {
                    // partly e.g. if $locale = "ja" or "haw"
                    // get first occurrence of $locale in $possibleLocales
                    if (!empty($matches = preg_grep('/^'.$locale.'_\w+$/i', $possibleLocales))) {
                        //\Zend\Debug\Debug::Dump($matches);
                        $locale = reset($matches);
                        $this->setCookie($responseHeaders, 'locale', $locale, time() + 365 * 60 * 60 * 24, '/');  // now + 1 year
                        return $locale;
                    }
                }
            }
        }

        $this->setCookie($responseHeaders, 'locale', $defaultLocale, time() + 365 * 60 * 60 * 24, '/');  // now + 1 year

        return $defaultLocale;
    }

    public function onBootstrap($e)
    {
        //$start = microtime(true);
        $application = $e->getApplication();
        $sm = $application->getServiceManager();

        // get default locale
        $config = $sm->get('Config');
        if (array_key_exists('default_locale', $config['csn_translation_options'])) {
            $defaultLocale = $config['csn_translation_options']['default_locale'];
        } else {
            $defaultLocale = "en_US";
        }

       // Check environment for Doctrine to prevent
       // "Fatal error: Call to undefined method Zend\Console\Request::getCookie()"
       // when we run "orm:schema-tool" in the Console
        if ($e->getRequest() instanceof \Zend\Http\Request) {
            $requestHeaders = $e->getRequest()->getHeaders();
            $responseHeaders = $e->getResponse()->getHeaders();

            //$entityManager = $sm->get('doctrine.entitymanager.orm_default');
            $entityManager = null;

            $locale = $this->getLocaleFromRequest($requestHeaders, $responseHeaders, $entityManager, $defaultLocale);
        } else {
            $locale = $defaultLocale;
        }

        $translator = $sm->get('translator');
        $translator->setLocale($locale)->setFallbackLocale($defaultLocale);

        // Add translations for Zend_Validate
        $folderName = substr($locale, 0, stripos($locale, '_'));
        if (file_exists($file = './vendor/zendframework/zendframework/resources/languages/'.$folderName.'/Zend_Captcha.php')) {
            $translator->addTranslationFile(
                'phpArray',
                $file,
                'default',
                $locale
            );
        }
        if (file_exists($file = './vendor/zendframework/zendframework/resources/languages/'.$folderName.'/Zend_Validate.php')) {
            $translator->addTranslationFile(
                'phpArray',
                $file,
                'default',
                $locale
            );

            \Zend\Validator\AbstractValidator::setDefaultTranslator($translator);
        }

        // Add translations from Database
        $this->configureDBTranslator($translator, $sm);

        // Provide access to current language in layout
        $language = $folderName;
        $eventManager    = $application->getEventManager();
        $events = $eventManager->getSharedManager();
        $events->attach('Zend\Mvc\Controller\AbstractActionController', 'dispatch', function ($e) use ($language) {
            $controller      = $e->getTarget();
            $controller->layout()->language = $language;
        }, 100);

        /*$end = microtime(true);
        print "<br><br><br><br>CsnTranslation loaded in ".round(($end - $start), 3)." seconds";*/
    }

    public function getDBTranslatorConfig()
    {
        return array(
            'factories' => array(
                'DoctrineTranslationLoader' => 'CsnTranslation\Service\Factory\DoctrineTranslationLoaderFactory',
            ),
        );
    }

    public function configureDBTranslator($translator, $sm)
    {
        $plugins = $translator->getPluginManager();
        $plugins->setServiceLocator($sm);

        $config  = new \Zend\ServiceManager\Config($this->getDBTranslatorConfig());
        $config->configureServiceManager($plugins);
    }

    public function getConfig()
    {
        return include __DIR__.'/../../config/module.config.php';
    }

    public function getAutoloaderConfig()
    {
        return array(
            'Zend\Loader\StandardAutoloader' => array(
                'namespaces' => array(
                    __NAMESPACE__ => __DIR__.'/../../src/'.__NAMESPACE__,
                ),
            ),
        );
    }
}
